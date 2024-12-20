<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>CVs are dumb</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite('resources/css/app.css')
</head>
<body class="h-full bg-gray-300">
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8" id="app">
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <div>
            <h1 class="font-mono text-2xl text-red-600 mb-4">Hate making CVs?</h1>
            <p class="font-mono text-xl text-slate-700 mb-4">Good. You don't have to do it no more. Nobody reads them
                anyways, they have no value but for some reason companies still want them</p>
            <p class="font-mono text-xl text-slate-700 mb-4">Use this tool to generate a document 'fine tuned' for any
                position you look for</p>
            <p class="font-mono text-sm text-red-700 mb-4">This tool will download a new CV for you. <span class="font-bold">Don't panic</span>. It's definetly not a virus. Trust me, I'm a random guy on the internet.</p>
        </div>

        <form class="space-y-6" method="POST" enctype="multipart/form-data" id="generate_cv_form">
            @csrf
            <div class="col-span-full">
                <label for="cv_upload" class="block text-sm/6 font-medium text-gray-900 text-mono">Drop your CV</label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10 bg-white drop-area">
                    <div class="text-center">
                        <svg class="mx-auto size-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor"
                             aria-hidden="true" data-slot="icon" id="cv_icon">
                            <path fill-rule="evenodd"
                                  d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <div class="mt-4 flex text-sm/6 text-gray-600">
                            <label for="cv_upload"
                                   class="relative cursor-pointer rounded-md bg-white font-semibold text-red-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-red-600 focus-within:ring-offset-2 hover:text-red-500">
                                <span id="cv_upload_cta">Upload a file</span>
                                <input id="cv_upload" name="cv_upload" type="file" class="sr-only">
                            </label>
                            <p class="pl-1" id="cv_upload_drag_and_drop">or drag and drop</p>
                        </div>
                        <p class="text-xs/5 text-gray-600" id="cv_file_types">TXT, PDF up to 10MB</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <label for="offer_description" class="block text-sm/6 font-medium text-gray-900 text-mono">Offer
                        description</label>
                </div>
                <div class="mt-2">
                    <textarea type="text" name="offer_description" id="offer_description" required class="block resize-y w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-red-600 sm:text-sm/6"></textarea>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="flex w-full justify-center rounded-md bg-red-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                    Generate!
                </button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm/6 text-gray-700">
            Looking for employees instead?
            <a href="https://woodford.work" class="font-semibold text-red-600 hover:text-red-800">Check us out</a>
        </p>
    </div>
</div>


{{-- Kudos for great loader: https://codepen.io/kira-code/pen/BaYGwYg --}}
<section class="sec-loading flex flex-col" id="loader">
    <div class="one">
    </div>
    <div class="mt-10 text-mono text-2xl text-red-600">
        <span id="text"></span>
    </div>
</section>

<script>
    const dropArea = document.querySelector('.drop-area');
    const fileInput = document.querySelector('#cv_upload');

    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.classList.add('dragover');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('dragover');
    });

    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            updateFileDetails(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        updateFileDetails(e.target.files[0]);
    });

    const updateFileDetails = (file) => {
        document.querySelector('#cv_upload_cta').innerText = file.name;
        document.querySelector('#cv_upload_drag_and_drop').innerText = '';
        document.querySelector('#cv_file_types').innerText = '';
        document.querySelector('#cv_icon').classList.remove('text-gray-300');
        document.querySelector('#cv_icon').classList.add('text-red-600');
    };


    document.querySelector('#cv_upload')?.addEventListener('change', (e) => {
        document.querySelector('#cv_upload_cta').innerText = e.target.files[0].name
        document.querySelector('#cv_upload_drag_and_drop').innerText = ''
        document.querySelector('#cv_file_types').innerText = ''
        document.querySelector('#cv_icon').classList.remove('text-gray-300')
        document.querySelector('#cv_icon').classList.add('text-red-600')
    })

    const loadingOn = () => {
        document.querySelector('#app').style.display = 'none'
        document.querySelector('#loader').style.display = 'flex'
    }

    const loadingOff = () => {
        document.querySelector('#app').style.display = 'flex'
        document.querySelector('#loader').style.display = 'none'
    }

    const excuses = [
        "Our AI is taking a coffee break.",
        "The server is having a nap.",
        "We are recalibrating the flux capacitor.",
        "The hamster powering our server needs a rest.",
        "We are optimizing your CV for maximum impact.",
        "The internet tubes are clogged.",
        "We are aligning the bits and bytes.",
        "The electrons are being stubborn today."
    ];

    const changeExcuse = () => {
        const textElement = document.querySelector('#text');
        textElement.classList.remove('show');
        setTimeout(() => {
            const randomIndex = Math.floor(Math.random() * excuses.length);
            textElement.innerText = excuses[randomIndex];
            textElement.classList.add('show');
        }, 500); // Match the transition duration
    };

    document.addEventListener('DOMContentLoaded', () => {
        const textElement = document.querySelector('#text');
        textElement.classList.add('fade');
        changeExcuse(); // Initial excuse
        setInterval(changeExcuse, 3000); // Change every 3 seconds
    });

    document.querySelector('#generate_cv_form').addEventListener('submit', async (e) => {
        e.preventDefault()

        loadingOn()

        const formData = new FormData()
        formData.append('cv_upload', document.querySelector('#cv_upload').files[0])
        formData.append('offer_description', document.querySelector('#offer_description').value)

        const token = document.querySelector('meta[name="csrf-token"]').content
        const res = await fetch(`{{ route('generate.document') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token
            },
            body: formData
        })

        if (!res.ok || res.status !== 200) {
            alert('Something went wrong. Try again later, or leave a message at https://github.com/kubabialy/cvsaredumb')
            loadingOff()
            return
        } else {
            const blob = await res.blob()
            const url = window.URL.createObjectURL(blob)
            const a = document.createElement('a')
            a.href = url
            a.download = 'document.pdf'
            document.body.appendChild(a)
            a.click()
            window.URL.revokeObjectURL(url)
        }
        loadingOff()
    })
</script>
</body>
</html>
