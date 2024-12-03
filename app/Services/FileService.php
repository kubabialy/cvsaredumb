<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Spatie\PdfToText\Exceptions\PdfNotFound;
use Spatie\PdfToText\Pdf;

final class FileService
{
    /**
     * @param UploadedFile $file
     * @return string
     * @throws PdfNotFound
     */
    public function extractData(UploadedFile $file): string
    {
        switch ($file->extension()) {
            case "txt":
                return $this->extractTxt($file);
            case "pdf":
                return $this->extractPdf($file);
            default:
                throw new InvalidArgumentException("Unsupported file type");
        }
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws PdfNotFound
     */
    private function extractPdf(UploadedFile $file): string
    {
        return new Pdf()->setPdf($file->path())->text();
    }

    private function extractTxt(UploadedFile $file): string
    {
        return $file->getContent();
    }
}
