<?php

namespace vaersaagod\toolmate\twigextensions;

use Twig\Compiler;
use Twig\Node\Node;
use vaersaagod\toolmate\ToolMate;

/**
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class MinifyNode extends Node
{
    // Public Methods
    // =========================================================================

    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $html = $this->getAttribute('html');
        $css = $this->getAttribute('css');
        $js = $this->getAttribute('js');

        $compiler
            ->addDebugInfo($this);

        $compiler
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$_compiledBody = ob_get_clean();\n");

        
        if ($html) {
            $compiler
                ->write('echo ' . ToolMate::class . "::\$plugin->minify->html(\$_compiledBody);\n");
        } elseif ($css) {
            $compiler
                ->write('echo ' . ToolMate::class . "::\$plugin->minify->css(\$_compiledBody);\n");
        } elseif ($js) {
            $compiler
                ->write('echo ' . ToolMate::class . "::\$plugin->minify->js(\$_compiledBody);\n");
        } else {
            $compiler
                ->write('echo ' . ToolMate::class . "::\$plugin->minify->minify(\$_compiledBody);\n");
        }
    }
}
