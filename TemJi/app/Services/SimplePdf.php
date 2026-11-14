<?php

namespace App\Services;

class SimplePdf
{
    protected string $buffer = '';
    protected array $pageObjects = [];
    protected int $currentPage = 0;
    protected string $pageContent = '';

    public function __construct()
    {
        $this->buffer = "%PDF-1.4\n";
    }

    public function addPage(): void
    {
        if ($this->currentPage > 0) {
            $this->endPage();
        }
        $this->currentPage++;
        $this->pageContent = '';
    }

    public function writeText(float $x, float $y, string $text, float $size = 12, bool $bold = false, float $r = 0.1, float $g = 0.1, float $b = 0.1): void
    {
        $font = $bold ? '/F2' : '/F1';
        $safeText = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $this->pageContent .= sprintf("%F %F %F rg BT %s %F Tf %F %F Td (%s) Tj ET\n", $r, $g, $b, $font, $size, $x, $y, $safeText);
    }

    public function drawRect(float $x, float $y, float $w, float $h, float $r = 0.95, float $g = 0.95, float $b = 0.95, bool $fill = true): void
    {
        if ($fill) {
            $this->pageContent .= sprintf("%F %F %F rg %F %F %F %F re f\n", $r, $g, $b, $x, $y, $w, $h);
        } else {
            $this->pageContent .= sprintf("%F %F %F RG %F %F %F %F re S\n", $r, $g, $b, $x, $y, $w, $h);
        }
    }

    public function drawLine(float $x1, float $y1, float $x2, float $y2, float $r = 0.8, float $g = 0.8, float $b = 0.8, float $width = 1.0): void
    {
        $this->pageContent .= sprintf("%F w %F %F %F RG %F %F m %F %F l S\n", $width, $r, $g, $b, $x1, $y1, $x2, $y2);
    }

    protected function endPage(): void
    {
        $this->pageObjects[] = $this->pageContent;
    }

    public function output(): string
    {
        if ($this->currentPage > 0) {
            $this->endPage();
        }

        $objCount = 4;
        $pageObjIds = [];
        $contentObjIds = [];

        foreach ($this->pageObjects as $content) {
            $objCount++;
            $pageObjIds[] = $objCount;
            $objCount++;
            $contentObjIds[] = $objCount;
        }

        $out = "%PDF-1.4\n";
        $offsets = [];

        // 1: Catalog
        $offsets[1] = strlen($out);
        $out .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

        // 2: Pages
        $offsets[2] = strlen($out);
        $kids = implode(' ', array_map(fn($id) => "$id 0 R", $pageObjIds));
        $out .= "2 0 obj\n<< /Type /Pages /Kids [$kids] /Count " . count($pageObjIds) . " >>\nendobj\n";

        // 3: Font Helvetica Regular
        $offsets[3] = strlen($out);
        $out .= "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n";

        // 4: Font Helvetica Bold
        $offsets[4] = strlen($out);
        $out .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>\nendobj\n";

        for ($i = 0; $i < count($this->pageObjects); $i++) {
            $pId = $pageObjIds[$i];
            $cId = $contentObjIds[$i];
            $content = $this->pageObjects[$i];

            $offsets[$pId] = strlen($out);
            $out .= "$pId 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents $cId 0 R >>\nendobj\n";

            $offsets[$cId] = strlen($out);
            $len = strlen($content);
            $out .= "$cId 0 obj\n<< /Length $len >>\nstream\n" . $content . "endstream\nendobj\n";
        }

        $xrefOffset = strlen($out);
        $totalObjs = $objCount + 1;
        $out .= "xref\n0 $totalObjs\n";
        $out .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $objCount; $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $out .= "trailer\n<< /Size $totalObjs /Root 1 0 R >>\nstartxref\n$xrefOffset\n%%EOF\n";

        return $out;
    }
}
