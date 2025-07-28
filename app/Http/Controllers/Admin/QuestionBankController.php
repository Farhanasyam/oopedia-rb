<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionBankConfig;
use App\Models\Question;
use App\Models\Material;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the question banks.
     */
    public function index(Request $request)
    {
        // Hanya superadmin dan admin yang boleh akses
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke bank soal');
        }
        
        $query = QuestionBank::query()->with('creator');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $questionBanks = $query->latest()->paginate(10);
        
        return view('admin.question-banks.index', compact('questionBanks'));
    }

    /**
     * Show the form for creating a new question bank.
     */
    public function create()
    {
        $materials = Material::all();
        return view('admin.question-banks.create', compact('materials'));
    }

    /**
     * Store a newly created question bank in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_id' => 'required|exists:materials,id',
        ]);
        
        $questionBank = new QuestionBank([
            'name' => $request->name,
            'description' => $request->description,
            'material_id' => $request->material_id,
            'created_by' => auth()->id(),
        ]);
        
        $questionBank->save();
        
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil dibuat.');
    }

    /**
     * Display the specified question bank.
     */
    public function show(QuestionBank $questionBank)
    {
        $questionBank->load(['questions.material', 'questions.answers', 'configs.material']);
        $questions = $questionBank->questions;
        
        // Count questions by difficulty
        $questionCounts = [
            'beginner' => $questions->where('difficulty', 'beginner')->count(),
            'medium' => $questions->where('difficulty', 'medium')->count(),
            'hard' => $questions->where('difficulty', 'hard')->count(),
        ];

        return view('admin.question-banks.show', compact('questionBank', 'questionCounts'));
    }

    /**
     * Show the form for editing the specified question bank.
     */
    public function edit(QuestionBank $questionBank)
    {
        return view('admin.question-banks.edit', compact('questionBank'));
    }

    /**
     * Update the specified question bank in storage.
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $questionBank->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil diperbarui.');
    }

    /**
     * Remove the specified question bank from storage.
     */
    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();
        
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil dihapus.');
    }

    /**
     * Show questions that can be added to the bank.
     */
    public function manageQuestions(QuestionBank $questionBank, Request $request)
    {
        $search = $request->input('search');
        $difficulty = $request->input('difficulty');
        
        // Get existing question IDs in this bank
        $existingQuestionIds = $questionBank->questions->pluck('id')->toArray();
        
        // Query to get questions not in the bank yet and from the same material
        $questionsQuery = Question::with(['material', 'answers'])
            ->where('material_id', $questionBank->material_id)
            ->whereNotIn('id', $existingQuestionIds);
            
        // Apply filters
        if ($search) {
            $questionsQuery->where('question_text', 'like', "%{$search}%");
        }
        
        if ($difficulty) {
            $questionsQuery->where('difficulty', $difficulty);
        }
        
        $questions = $questionsQuery->paginate(10);
        
        return view('admin.question-banks.manage-questions', compact(
            'questionBank', 
            'questions'
        ));
    }
    
    /**
     * Add a question to the bank.
     */
    public function addQuestion(QuestionBank $questionBank, Question $question)
    {
        // Check if question already exists in the bank and from same material
        if ($question->material_id != $questionBank->material_id) {
            return redirect()->back()->with('error', 'Soal tidak dapat ditambahkan karena tidak sesuai dengan materi bank soal.');
        }
        
        if (!$questionBank->questions->contains($question->id)) {
            $questionBank->questions()->attach($question->id);
            return redirect()->back()->with('success', 'Soal berhasil ditambahkan ke bank soal.');
        }
        
        return redirect()->back()->with('error', 'Soal sudah ada dalam bank soal.');
    }
    
    /**
     * Remove a question from the bank.
     */
    public function removeQuestion(QuestionBank $questionBank, Question $question)
    {
        $questionBank->questions()->detach($question->id);
        return redirect()->back()->with('success', 'Soal berhasil dihapus dari bank soal.');
    }
    
    /**
     * Show configuration form for question bank.
     */
    public function configureBank(QuestionBank $questionBank, Request $request)
    {
        $materials = Material::all();
        $configs = $questionBank->configs()->with('material')->get();
        
        // Handle edit mode
        $editConfig = null;
        if ($request->has('edit')) {
            $editConfig = QuestionBankConfig::where('id', $request->edit)
                ->where('question_bank_id', $questionBank->id)
                ->firstOrFail();
        }
        
        return view('admin.question-banks.configure', compact('questionBank', 'materials', 'configs', 'editConfig'));
    }
    
    /**
     * Store bank configuration
     */
    public function storeConfig(Request $request, QuestionBank $questionBank)
    {
        // Validasi data
        $rules = [
            'beginner_count' => 'required|integer|min:0',
            'medium_count' => 'required|integer|min:0',
            'hard_count' => 'required|integer|min:0',
        ];
        
        // Jika ini konfigurasi baru, wajib pilih materi
        if (!$request->has('config_id')) {
            $rules['material_id'] = 'required|exists:materials,id';
        }
        
        $request->validate($rules);
        
        // Pastikan ada minimal satu soal yang diatur
        $totalQuestions = (int)$request->beginner_count + (int)$request->medium_count + (int)$request->hard_count;
        if ($totalQuestions <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total soal harus lebih dari 0');
        }
        
        // Update atau create konfigurasi
        if ($request->has('config_id')) {
            // Update existing config
            $config = QuestionBankConfig::findOrFail($request->config_id);
            
            // Verifikasi bahwa konfigurasi ini milik bank soal yang benar
            if ($config->question_bank_id != $questionBank->id) {
                return redirect()->back()
                    ->with('error', 'Konfigurasi tidak ditemukan');
            }
            
            $config->update([
                'beginner_count' => $request->beginner_count,
                'medium_count' => $request->medium_count,
                'hard_count' => $request->hard_count,
                'is_active' => $request->has('is_active'),
            ]);
            $message = 'Konfigurasi bank soal berhasil diperbarui.';
        } else {
            // Check if config already exists for this material
            $existingConfig = QuestionBankConfig::where('question_bank_id', $questionBank->id)
                ->where('material_id', $request->material_id)
                ->first();
                
            if ($existingConfig) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Konfigurasi untuk materi ini sudah ada.');
            }
            
            // Create new config
            QuestionBankConfig::create([
                'question_bank_id' => $questionBank->id,
                'material_id' => $request->material_id,
                'beginner_count' => $request->beginner_count,
                'medium_count' => $request->medium_count,
                'hard_count' => $request->hard_count,
                'is_active' => $request->has('is_active'),
            ]);
            $message = 'Konfigurasi bank soal berhasil ditambahkan.';
        }
        
        return redirect()->route('admin.question-banks.configure', $questionBank)
            ->with('success', $message);
    }
    
    /**
     * Delete a bank configuration.
     */
    public function deleteConfig(QuestionBankConfig $config)
    {
        $questionBankId = $config->question_bank_id;
        $config->delete();
        
        return redirect()->route('admin.question-banks.configure', $questionBankId)
            ->with('success', 'Konfigurasi bank soal berhasil dihapus.');
    }
}
