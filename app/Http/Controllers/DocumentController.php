<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Services\Gepetto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Throwable;

class DocumentController extends Controller
{
    private const string CV_KEY = "cv_upload";
    private const string OFFER_KEY = "offer_description";

    public function __construct(
        private readonly FileService $fileService,
        private readonly Gepetto $gepetto
    ) {}

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view("document.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     * @throws Throwable if the GPT_API_KEY is not set
     */
    public function generate(Request $request): Response
    {
        // TODO: Move the validation to Laravel form request
        $validatedData = $request->validateWithBag("document", [
            self::CV_KEY => "required|mimes:txt,pdf|max:10240",
            self::OFFER_KEY => "required|min:10",
        ]);

        /** @var UploadedFile $file */
        $file = $validatedData[self::CV_KEY];

        $fileContents = $this->fileService->extractData($file);
        $contents = $this->gepetto
            ->generateCVContent(
                currentCVContent: $fileContents,
                offerDescription: $validatedData[self::OFFER_KEY]
            );

        // This is where the 'magic' happens. We render HTML using blade templating language and then convert it to PDF.
        // Theoretically it should allow us to use CSS and other HTML features to style the document.
        $pdf = Pdf::loadHTML(view('document.template', compact('contents'))->render());

        return $pdf->download(sprintf('cv-%s.pdf', $contents->personal->name));
    }
}
