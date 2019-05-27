<?php


namespace App\Services\Reflection;


use App\ActivityReflection;
use App\ActivityReflectionField;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;

class Exporter
{

    /**
     * @param ActivityReflection[] $reflections
     * @return PhpWord
     */
    public function exportReflections(array $reflections): PhpWord
    {
        $document = $this->getNewDocument();
        $section = $document->addSection();


        $document->addTitleStyle(1, ['size' => 16, 'bold' => true]);

        $fieldNameStyle = new Font();
        $fieldNameStyle->setBold();

        foreach ($reflections as $reflection) {
            $section->addTitle(__('reflection.reflection') . ': ' . $reflection->reflection_type);
            $section->addText('', [], ['borderBottomSize' => 6]);
            $section->addTextBreak();

            /** @var ActivityReflectionField $field */
            foreach ($reflection->fields as $field) {
                $section->addText(ucfirst($field->name), $fieldNameStyle);
                $section->addTextBreak();
                $section->addText($this->processText($field->value));
                $section->addTextBreak(2);
            }
            $section->addTextBreak(3);


        }

        return $document;
    }

    private function processText(string $text)
    {
        $text = htmlspecialchars($text);

        return preg_replace('~\R~u', '</w:t><w:br/><w:t>', $text);
    }

    private function getNewDocument(): PhpWord
    {
        return new PhpWord();
    }


}