<?php

namespace Blep\Generator;

class TemplateRenderer
{
    private ?string $templateFolder;

    public function __construct(?string $templateFolder = null)
    {
        $this->templateFolder = $templateFolder !== null ? rtrim($templateFolder, '/') : null;
    }

    public function render(string $templateName, array $vars): string
    {
        $path = $this->resolveTemplate($templateName);

        if ($templateName === 'styles.css') {
            return '<style>' . file_get_contents($path) . '</style>';
        }

        return $this->renderFromFile($path, $vars);
    }

    private function resolveTemplate(string $name): string
    {
        if ($this->templateFolder !== null) {
            $override = $this->templateFolder . '/' . $name;
            if (file_exists($override)) {
                return $override;
            }
        }

        return __DIR__ . '/../Templates/' . $name;
    }

    private function renderFromFile(string $filePath, array $vars): string
    {
        extract($vars, EXTR_SKIP);
        ob_start();
        include $filePath;
        return (string) ob_get_clean();
    }
}
