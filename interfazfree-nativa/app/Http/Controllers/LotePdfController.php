<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Mpdf\Mpdf;

class LotePdfController extends Controller
{
    public function generate(Lote $lote)
    {
        $lote->load(['fichas', 'perfil', 'nas']);
        
        $fichasData = $lote->fichas->take(52)->map(function ($ficha) {
            return [
                'perfil' => $ficha->perfil->nombre ?? 'N/A',
                'usuario' => $ficha->username,
                'clave' => $ficha->password,
            ];
        });
        
        $html = view('pdf.lote', [
            'lote' => $lote,
            'fichas' => $fichasData,
        ])->render();
        
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
        ]);
        
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output("lote-{$lote->nombre}.pdf", 'D');
    }
}
