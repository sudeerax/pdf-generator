<?php
/**
 * Created by PhpStorm.
 * User: Sabeywickrema
 * Date: 19/01/2021
 * Time: 12:31 PM
 */

namespace PdfGenerator;

use Spatie\Browsershot\Browsershot;

class PdfGenerator
{

    public static function getPdf($request)
    {
        if($request->savedMapLink)
        {
            $random_string = md5(uniqid(rand(), true));
            $file_name = $random_string . '.pdf';
            $url = $request->savedMapLink;

            Browsershot::url($url)
                ->format($request->print_format)
                ->setOption('landscape', $request->print_layout == 'landscape' ? true : false)
                ->deviceScaleFactor($request->resolution)
                ->setNodeBinary('/usr/local/bin/node')
                ->setNpmBinary('/usr/local/bin/npm')
                //->setDelay('8000')
                ->timeout(10000)
                ->waitUntilNetworkIdle()
                ->noSandbox()
                ->save($file_name);

            //For Laravel download and delete file
            //return response()->download(storage_path('app/public/' . $random_string . '.pdf'))->deleteFileAfterSend(true);

            if (file_exists($file_name))
            {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file_name));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_name));
                ob_clean();
                flush();
                readfile($file_name);

                //DELETE file after download?
                unlink($file_name);
                exit;
            }
        }
    }
}