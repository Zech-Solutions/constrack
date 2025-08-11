<?php

namespace App\Jobs;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateQuotation implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(private readonly Quotation $quotation) {}

    public function handle(): void
    {
        File::ensureDirectoryExists(storage_path('app/public/quotations'));

        $path = storage_path('app/public/tenant/logo/ywbecs.png');

        if (file_exists($path)) {
            $imageData = base64_encode(file_get_contents($path));
            $mimeType = mime_content_type($path);
            $logo = "data:$mimeType;base64,$imageData";
        } else {
            $logo = null;
        }

        $pdf = Pdf::loadView('quotations.quotation', [
            'quotation' => $this->quotation,
            'logo' => $logo,
        ]);

        $filename = Str::uuid().'.pdf';

        $pdf->save(storage_path('app/public/quotations/'.$filename));

        $this->quotation->update(['filename' => $filename]);
    }
}
