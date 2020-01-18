<?php

namespace App\Model;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow   = 0;
    private $columns  = [];

    public function setRows($startRow, $chunkSize) {
        $this->startRow = $startRow;
        $this->endRow   = $startRow + $chunkSize;
    }

    public function setColumns($columns) {
        $this->columns  = $columns;
    }

    public function readCell($column, $row, $worksheetName = '') {
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }
        return false;
    }
}