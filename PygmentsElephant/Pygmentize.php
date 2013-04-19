<?php
/**
 * User: matteo
 * Date: 03/12/12
 * Time: 23.41
 * 
 * Just for fun...
 */

namespace Cypress\PygmentsElephantBundle\PygmentsElephant;

use Cypress\PygmentsElephantBundle\PygmentsElephant\PygmentizeBinary;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Pygmentize wrapper
 */
class Pygmentize
{
    /**
     * @var PygmentizeBinary
     */
    private $binary;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $lexer;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * Class constructor
     *
     * @param PygmentizeBinary $binary binary
     */
    public function __construct(PygmentizeBinary $binary)
    {
        $this->binary = $binary;
        $this->fs = new Filesystem();
    }

    /**
     * format a file by its name
     *
     * @param string $filename file name
     * @param string $lexer    lexer
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function formatFile($filename, $lexer = null)
    {
        if (!$this->fs->exists($filename)) {
            throw new \InvalidArgumentException(sprintf('the file %s doesn\'t exists', $filename));
        }
        $this->subject = $filename;
        $this->lexer = null === $lexer ? $this->binary->guessLexer($filename) : $lexer;
        $this->format = 'html';

        return $this->binary->execute($this);
    }

    /**
     * format a string of content
     *
     * @param string $content          content
     * @param string $originalFilename original filename for the lexer guesser
     *
     * @internal param string $lexer lexer
     * @internal param string $format format
     *
     * @internal param string $filename file name
     * @return string
     */
    public function format($content, $originalFilename)
    {
        $pathInfo = pathinfo($originalFilename);
        $filename = sys_get_temp_dir().'/pygmentize_'.sha1(uniqid()).(isset($pathInfo['extension']) ? '.'. $pathInfo['extension'] : '');
        $this->fs->touch($filename);
        $h = fopen($filename, 'w');
        fwrite($h, $content);
        fclose($h);

        return $this->formatFile($filename, $this->binary->guessLexer($filename));
    }

    /**
     * generate css for pygments html
     *
     * @param string $theme     theme name, 'default', 'emacs', 'friendly', 'colorful'
     * @param string $baseClass base css class
     *
     * @return string
     * @throws \RuntimeException
     */
    public function generateCss($theme = 'default', $baseClass = 'highlight')
    {
        return $this->binary->executeCommand(sprintf('-f html -S %s -a .%s', $theme, $baseClass));
    }

    /**
     * Set Format
     *
     * @param string $format the format variable
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * Get Format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set Lexer
     *
     * @param string $lexer the lexer variable
     */
    public function setLexer($lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Get Lexer
     *
     * @return string
     */
    public function getLexer()
    {
        return $this->lexer;
    }

    /**
     * Set Subject
     *
     * @param string $subject the subject variable
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get Subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
