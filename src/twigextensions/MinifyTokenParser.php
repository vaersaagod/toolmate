<?php

namespace vaersaagod\toolmate\twigextensions;

use Twig\Token;
use vaersaagod\toolmate\twigextensions\MinifyNode;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Minify twig token parser
 *
 * @author    Værsågod
 * @package   ToolMate
 * @since     1.0.0
 */
class MinifyTokenParser extends AbstractTokenParser
{
    // Public Methods
    // =========================================================================

    public function parse(Token $token)
    {
        $lineNo = $token->getLine();
        $stream = $this->parser->getStream();

        $attributes = [
            'html' => false,
            'css' => false,
            'js' => false,
        ];

        if ($stream->test(Token::NAME_TYPE, 'html')) {
            $attributes['html'] = true;
            $stream->next();
        }

        if ($stream->test(Token::NAME_TYPE, 'css')) {
            $attributes['css'] = true;
            $stream->next();
        }

        if ($stream->test(Token::NAME_TYPE, 'js')) {
            $attributes['js'] = true;
            $stream->next();
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $nodes['body'] = $this->parser->subparse([$this, 'testMinifyEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new MinifyNode($nodes, $attributes, $lineNo, $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'minify';
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function testMinifyEnd(Token $token): bool
    {
        return $token->test('endminify');
    }
}
