<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\UeqSurvey;
use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\Validator;

class UeqSurveyController extends Controller
{
    public function create()
    {
        // Check if user has already submitted a survey
        $existingSurvey = UeqSurvey::where('user_id', auth()->id())->first();
        if ($existingSurvey) {
            return redirect()->route('mahasiswa.ueq.thankyou');
        }

        $aspects = [
            ['name' => 'annoying_enjoyable'],
            ['name' => 'not_understandable_understandable'],
            ['name' => 'creative_dull'],
            ['name' => 'easy_difficult'],
            ['name' => 'valuable_inferior'],
            ['name' => 'boring_exciting'],
            ['name' => 'not_interesting_interesting'],
            ['name' => 'unpredictable_predictable'],
            ['name' => 'fast_slow'],
            ['name' => 'inventive_conventional'],
            ['name' => 'obstructive_supportive'],
            ['name' => 'good_bad'],
            ['name' => 'complicated_easy'],
            ['name' => 'unlikable_pleasing'],
            ['name' => 'usual_leading_edge'],
            ['name' => 'unpleasant_pleasant'],
            ['name' => 'secure_not_secure'],
            ['name' => 'motivating_demotivating'],
            ['name' => 'meets_expectations_does_not_meet'],
            ['name' => 'inefficient_efficient'],
            ['name' => 'clear_confusing'],
            ['name' => 'impractical_practical'],
            ['name' => 'organized_cluttered'],
            ['name' => 'attractive_unattractive'],
            ['name' => 'friendly_unfriendly'],
            ['name' => 'conservative_innovative']
        ];
        
        return view('mahasiswa.ueq.create', compact('aspects'));
    }

    public function store(Request $request)
    {
        // Check if user has already submitted a survey
        $existingSurvey = UeqSurvey::where('user_id', auth()->id())->first();
        if ($existingSurvey) {
            return redirect()->route('mahasiswa.ueq.thankyou');
        }

        // Tambahkan validasi untuk kolom baru
        $rules = $this->getValidationRules();
        $rules['nim'] = 'required|string|max:20';
        $rules['class'] = 'required|string|max:20';
        
        $messages = $this->getValidationMessages();
        $messages['nim.required'] = 'NIM wajib diisi';
        $messages['class.required'] = 'Kelas wajib diisi';
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('missingFields', $validator->errors()->keys());
        }

        // Buat record survey baru
        $survey = new UeqSurvey();
        $survey->user_id = auth()->id();
        $survey->nim = $request->nim;
        $survey->class = $request->class;
        
        // Set semua aspek UEQ (kode yang sudah ada)
        foreach ($this->getAspects() as $aspect) {
            $name = $aspect['name'];
            $survey->{$name} = $request->input($name);
        }
        
        $survey->comments = $request->comments;
        $survey->suggestions = $request->suggestions;
        $survey->save();

        return redirect()->route('mahasiswa.ueq.thankyou');
    }
    
    /**
     * Check for empty fields in the request
     */
    private function checkEmptyFields(Request $request)
    {
        $emptyFields = [];
        
        // Get all UEQ aspect fields (except comments and suggestions)
        $fieldNames = [
            'annoying_enjoyable', 'not_understandable_understandable', 'creative_dull',
            'easy_difficult', 'valuable_inferior', 'boring_exciting',
            'not_interesting_interesting', 'unpredictable_predictable', 'fast_slow',
            'inventive_conventional', 'obstructive_supportive', 'good_bad',
            'complicated_easy', 'unlikable_pleasing', 'usual_leading_edge',
            'unpleasant_pleasant', 'secure_not_secure', 'motivating_demotivating',
            'meets_expectations_does_not_meet', 'inefficient_efficient', 'clear_confusing',
            'impractical_practical', 'organized_cluttered', 'attractive_unattractive',
            'friendly_unfriendly', 'conservative_innovative'
        ];
        
        foreach ($fieldNames as $field) {
            if (!$request->has($field) || $request->input($field) === null) {
                $emptyFields[] = $field;
            }
        }
        
        return $emptyFields;
    }
    
    /**
     * Handle empty fields by redirecting with errors
     */
    private function handleEmptyFields(Request $request, array $emptyFields)
    {
        $errors = [];
        foreach ($emptyFields as $field) {
            $errors[$field] = ['Pertanyaan harus dijawab'];
        }
        
        return redirect()
            ->back()
            ->withErrors($errors)
            ->withInput($request->all())
            ->with('error', 'Ada ' . count($emptyFields) . ' pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.')
            ->with('missingFields', $emptyFields);
    }
    
    /**
     * Handle validation errors
     */
    private function handleValidationErrors(Request $request, $validator, array $missingFields)
    {
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput($request->all())
            ->with('error', 'Ada ' . count($missingFields) . ' pertanyaan yang belum dijawab atau tidak valid. Silakan periksa kembali.')
            ->with('missingFields', $missingFields);
    }
    
    /**
     * Get validation rules for all fields
     */
    private function getValidationRules()
    {
        return [
            'annoying_enjoyable' => 'required|integer|between:1,7',
            'not_understandable_understandable' => 'required|integer|between:1,7',
            'creative_dull' => 'required|integer|between:1,7',
            'easy_difficult' => 'required|integer|between:1,7',
            'valuable_inferior' => 'required|integer|between:1,7',
            'boring_exciting' => 'required|integer|between:1,7',
            'not_interesting_interesting' => 'required|integer|between:1,7',
            'unpredictable_predictable' => 'required|integer|between:1,7',
            'fast_slow' => 'required|integer|between:1,7',
            'inventive_conventional' => 'required|integer|between:1,7',
            'obstructive_supportive' => 'required|integer|between:1,7',
            'good_bad' => 'required|integer|between:1,7',
            'complicated_easy' => 'required|integer|between:1,7',
            'unlikable_pleasing' => 'required|integer|between:1,7',
            'usual_leading_edge' => 'required|integer|between:1,7',
            'unpleasant_pleasant' => 'required|integer|between:1,7',
            'secure_not_secure' => 'required|integer|between:1,7',
            'motivating_demotivating' => 'required|integer|between:1,7',
            'meets_expectations_does_not_meet' => 'required|integer|between:1,7',
            'inefficient_efficient' => 'required|integer|between:1,7',
            'clear_confusing' => 'required|integer|between:1,7',
            'impractical_practical' => 'required|integer|between:1,7',
            'organized_cluttered' => 'required|integer|between:1,7',
            'attractive_unattractive' => 'required|integer|between:1,7',
            'friendly_unfriendly' => 'required|integer|between:1,7',
            'conservative_innovative' => 'required|integer|between:1,7',
            'comments' => 'required|max:1000',
            'suggestions' => 'required|max:1000',
            'nim' => 'required|string|max:20',
            'class' => 'required|string|max:20'
        ];
    }
    
    private function getValidationMessages()
    {
        return [
            'annoying_enjoyable.required' => 'Skala penilaian antara Menyebalkan-Menyenangkan wajib diisi',
            'not_understandable_understandable.required' => 'Skala penilaian antara Tidak dapat dipahami-Dapat dipahami wajib diisi',
            'creative_dull.required' => 'Skala penilaian antara Kreatif-Monoton wajib diisi',
            'easy_difficult.required' => 'Skala penilaian antara Mudah-Sulit wajib diisi',
            'valuable_inferior.required' => 'Skala penilaian antara Bermanfaat-Kurang bermanfaat wajib diisi',
            'boring_exciting.required' => 'Skala penilaian antara Membosankan-Menarik wajib diisi',
            'not_interesting_interesting.required' => 'Skala penilaian antara Tidak menarik-Menarik wajib diisi',
            'unpredictable_predictable.required' => 'Skala penilaian antara Tidak dapat diprediksi-Dapat diprediksi wajib diisi',
            'fast_slow.required' => 'Skala penilaian antara Cepat-Lambat wajib diisi',
            'inventive_conventional.required' => 'Skala penilaian antara Inovatif-Konvensional wajib diisi',
            'obstructive_supportive.required' => 'Skala penilaian antara Menghambat-Mendukung wajib diisi',
            'good_bad.required' => 'Skala penilaian antara Baik-Buruk wajib diisi',
            'complicated_easy.required' => 'Skala penilaian antara Rumit-Sederhana wajib diisi',
            'unlikable_pleasing.required' => 'Skala penilaian antara Tidak disukai-Menyenangkan wajib diisi',
            'usual_leading_edge.required' => 'Skala penilaian antara Biasa saja-Terdepan wajib diisi',
            'unpleasant_pleasant.required' => 'Skala penilaian antara Tidak menyenangkan-Menyenangkan wajib diisi',
            'secure_not_secure.required' => 'Skala penilaian antara Aman-Tidak aman wajib diisi',
            'motivating_demotivating.required' => 'Skala penilaian antara Memotivasi-Tidak memotivasi wajib diisi',
            'meets_expectations_does_not_meet.required' => 'Skala penilaian antara Memenuhi ekspektasi-Tidak memenuhi ekspektasi wajib diisi',
            'inefficient_efficient.required' => 'Skala penilaian antara Tidak efisien-Efisien wajib diisi',
            'clear_confusing.required' => 'Skala penilaian antara Jelas-Membingungkan wajib diisi',
            'impractical_practical.required' => 'Skala penilaian antara Tidak praktis-Praktis wajib diisi',
            'organized_cluttered.required' => 'Skala penilaian antara Terorganisir-Berantakan wajib diisi',
            'attractive_unattractive.required' => 'Skala penilaian antara Menarik-Tidak menarik wajib diisi',
            'friendly_unfriendly.required' => 'Skala penilaian antara Ramah-Tidak ramah wajib diisi',
            'conservative_innovative.required' => 'Skala penilaian antara Konservatif-Inovatif wajib diisi',
            
            // Pesan validasi untuk batasan nilai (1-7)
            'annoying_enjoyable.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'not_understandable_understandable.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'creative_dull.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'easy_difficult.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'valuable_inferior.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'boring_exciting.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'not_interesting_interesting.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'unpredictable_predictable.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'fast_slow.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'inventive_conventional.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'obstructive_supportive.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'good_bad.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'complicated_easy.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'unlikable_pleasing.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'usual_leading_edge.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'unpleasant_pleasant.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'secure_not_secure.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'motivating_demotivating.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'meets_expectations_does_not_meet.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'inefficient_efficient.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'clear_confusing.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'impractical_practical.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'organized_cluttered.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'attractive_unattractive.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'friendly_unfriendly.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            'conservative_innovative.between' => 'Skala penilaian harus bernilai antara 1 sampai 7',
            
            // Validasi untuk komentar dan saran
            'comments.max' => 'Komentar tidak boleh lebih dari 1000 karakter',
            'suggestions.max' => 'Saran tidak boleh lebih dari 1000 karakter',
            'comments.required' => 'Komentar wajib diisi',
            'suggestions.required' => 'Saran wajib diisi',
            'nim.required' => 'NIM wajib diisi',
            'class.required' => 'Kelas wajib diisi'
        ];
    }
    
    public function thankyou()
    {
        return view('mahasiswa.ueq.thankyou');
    }

    /**
     * Get all UEQ survey aspects
     * 
     * @return array
     */
    private function getAspects()
    {
        return [
            ['name' => 'annoying_enjoyable'],
            ['name' => 'not_understandable_understandable'],
            ['name' => 'creative_dull'],
            ['name' => 'easy_difficult'],
            ['name' => 'valuable_inferior'],
            ['name' => 'boring_exciting'],
            ['name' => 'not_interesting_interesting'],
            ['name' => 'unpredictable_predictable'],
            ['name' => 'fast_slow'],
            ['name' => 'inventive_conventional'],
            ['name' => 'obstructive_supportive'],
            ['name' => 'good_bad'],
            ['name' => 'complicated_easy'],
            ['name' => 'unlikable_pleasing'],
            ['name' => 'usual_leading_edge'],
            ['name' => 'unpleasant_pleasant'],
            ['name' => 'secure_not_secure'],
            ['name' => 'motivating_demotivating'],
            ['name' => 'meets_expectations_does_not_meet'],
            ['name' => 'inefficient_efficient'],
            ['name' => 'clear_confusing'],
            ['name' => 'impractical_practical'],
            ['name' => 'organized_cluttered'],
            ['name' => 'attractive_unattractive'],
            ['name' => 'friendly_unfriendly'],
            ['name' => 'conservative_innovative']
        ];
    }
} 