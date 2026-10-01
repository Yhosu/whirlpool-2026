<?php

namespace App\Helpers;

use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\AbstractOptions;

class ExcelFunc
{
    public static function export($data, $filename)
    {
        $headerStyle = (new Style)
            ->setFontBold()
            ->setFontSize(11)
            ->setShouldWrapText()
            ->setBackgroundColor('EDEDED');

        return fastexcel($data)
            ->headerStyle($headerStyle)
            ->configureOptionsUsing(function (AbstractOptions $options) {
                // Set a default column width for all columns

                // Or set a specific column width for a range (e.g., column 1 to 1 with width 30)
                $options->setColumnWidthForRange(27, 2, 10);
            })
            ->download($filename);
    }

    public static function import($file, $callback)
    {
        $users = fastexcel()->import($file, $callback);

        return true;
    }
}
