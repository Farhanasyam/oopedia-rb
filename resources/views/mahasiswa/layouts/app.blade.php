<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OOPEDIA - @yield('title')</title>
    
    <link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
    <!-- <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script> -->

    <!-- Global CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Base CSS -->
    <link href="{{ asset('css/mahasiswa.css') }}" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/9iw2xqwn1593xsb15d6xpi0y41mtrets5ms0l5s8kekdgf63/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/exercise-feedback.css') }}">
    

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <style>
        /* TinyMCE Editor Improvements */
        .tox-tinymce {
            min-height: 400px !important;
            margin-bottom: 20px;
        }

        /* Content Display Styles */
        .content-display {
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            max-width: 100%;
            padding: 15px;
        }

        .content-display img {
            max-width: 100%;
            height: auto;
        }

        .content-display pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }

        .content-display code {
            background: #f5f5f5;
            padding: 2px 4px;
            border-radius: 4px;
        }

        /* Gambar Card Material */
        .material-image {
            height: 180px;
            position: relative;
            border-top-left-radius: 13px;
            border-top-right-radius: 13px;
            border-bottom: 1px solid #e0e6ed;
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .material-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Gambar Card Question */
        .material-left-section {
            width: 180px;
            min-width: 180px;
            height: 180px;
            background-color: #f8f9fa;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .material-question-image {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .material-question-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <!-- Additional page-specific CSS -->
    @stack('css')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Add this in the head section, before other scripts -->
    <script src="{{ asset('assets/tinymce/tinymce.min.js') }}"></script>
    <script>
        // Only initialize TinyMCE when in read-only view
        document.addEventListener('DOMContentLoaded', function() {
            // Apply content styling to elements with HTML content
            const contentElements = document.querySelectorAll('.question-text, .answer-text, .answer-explanation');
            
            contentElements.forEach(element => {
                // Ensure code blocks are properly formatted
                const codeBlocks = element.querySelectorAll('pre');
                codeBlocks.forEach(block => {
                    block.classList.add('language-java');
                    
                    // Add proper styling to code blocks
                    if (!block.classList.contains('formatted')) {
                        block.classList.add('formatted');
                        block.style.backgroundColor = '#f1f3f5';
                        block.style.padding = '1rem';
                        block.style.borderRadius = '4px';
                        block.style.fontFamily = 'monospace';
                        block.style.overflow = 'auto';
                    }
                });
                
                // Format tables if any
                const tables = element.querySelectorAll('table');
                tables.forEach(table => {
                    if (!table.classList.contains('formatted')) {
                        table.classList.add('formatted', 'table', 'table-bordered');
                    }
                });
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('css/loading-overlay.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Include Navbar Component -->
    @include('mahasiswa.components.navbar')

    <div class="container">
        <!-- Include Sidebar Component -->
        @include('mahasiswa.components.sidebar')

        <!-- Main Content -->
        <main class="main-content">
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- TinyMCE Initialization -->
    <script>
        tinymce.init({
            selector: 'textarea.tinymce',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            menubar: false,
            height: 400,
            content_style: `
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                    font-size: 16px;
                    line-height: 1.6;
                    color: #333;
                    margin: 15px;
                }
                p { margin: 0 0 1em 0; }
                img { max-width: 100%; height: auto; }
                pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
                code { background: #f5f5f5; padding: 2px 4px; border-radius: 4px; }
            `,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    </script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua popup login
            if (document.querySelector('.login-required-modal')) {
                document.querySelector('.login-required-modal').remove();
            }
            
            // Override behavior pesan login
            window.preventLoginRedirect = function() {
                return false;
            };
            
            // Override fungsi redirect ke login
            window.redirectToLogin = function() {
                return false;
            };
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Add Loading Overlay Component -->
    <x-loading-overlay />

    <!-- Loading Overlay JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loading-overlay');
        
        // Show loading on page load
        showLoading();
        
        // Hide when page is fully loaded
        window.addEventListener('load', function() {
            hideLoading();
        });
        
        // Show loading on navigation
        document.addEventListener('click', function(event) {
            const link = event.target.closest('a');
            if (link && 
                link.href && 
                !link.target && 
                link.hostname === window.location.hostname && 
                !link.hasAttribute('data-bs-toggle') && 
                !link.classList.contains('no-loading')) {
                showLoading();
            }
        });
        
        // Show loading on form submissions
        document.addEventListener('submit', function(event) {
            if (!event.target.classList.contains('ajax-form')) {
                showLoading();
            }
        });
        
        // Safety timeout to prevent infinite loading
        let loadingTimeout;
        
        // Helper functions
        window.showLoading = function() {
            loadingOverlay.classList.add('show');
            
            // Set safety timeout
            clearTimeout(loadingTimeout);
            loadingTimeout = setTimeout(() => {
                hideLoading();
            }, 10000); // 10 seconds max
        };
        
        window.hideLoading = function() {
            clearTimeout(loadingTimeout);
            loadingOverlay.classList.remove('show');
        };
    });
    </script>

    <!-- Lightbox untuk Galeri Gambar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "Gambar %1 dari %2"
        });
    </script>

    <!-- Sidebar backdrop for mobile -->
    <div class="sidebar-backdrop"></div>
</body>
</html>