<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;


class QuotationController extends Controller
{
    public function generatePdf($id)
    {

        $quotation = Quotation::find($id);
        $path = public_path('storage/tenant/logo/ywbecs.png');

        if (file_exists($path)) {
            $imageData = base64_encode(file_get_contents($path));
            $mimeType = mime_content_type($path); // e.g. "image/png"
            $logo = "data:$mimeType;base64,$imageData";
        } else {
            $logo = null;
        }

        // $pdf = Pdf::loadView('quotations.quotation',  compact('quotation' , 'logo'));
        // return $pdf->download('invoice.pdf');
        return view("quotations.quotation", compact('quotation', 'logo'));
    }
}
