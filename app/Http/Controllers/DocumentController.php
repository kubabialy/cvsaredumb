<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Services\Gepetto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

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
     */
    public function generate(Request $request): Response
    {
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
        $view = view('document.template', compact('contents'));

        $pdf = Pdf::loadHTML($view->render());

        return $pdf->download(sprintf('cv-%s.pdf', $contents->personal->name));
    }
}
