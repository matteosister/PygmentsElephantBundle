<?php
/**
 * User: matteo
 * Date: 03/12/12
 * Time: 23.29
 * 
 * Just for fun...
 */

namespace Cypress\PygmentsElephantBundle\PygmentsElephant;

use Symfony\Component\Process\Process;

/**
 * Pygmentize binary wrapper
 */
class PygmentizeBinary
{
    /**
     * @var array
     */
    private $fileTypes;

    /**
     * @var string
     */
    private $binaryName = 'pygmentize';

    /**
     * @var null|string
     */
    private $binaryPath;

    /**
     * class constructor
     *
     * @param array $fileTypes  configuration file types
     * @param null  $binaryPath the binary path
     */
    public function __construct($fileTypes = array(), $binaryPath = null)
    {
        $this->fileTypes = $fileTypes;
        $this->binaryPath = null === $binaryPath ? $this->guessBinaryPath() : $binaryPath;
    }

    /**
     * try to guess the binary path (*nix only)
     *
     * @return string
     * @throws \RuntimeException
     */
    private function guessBinaryPath()
    {
        $p = new Process(sprintf('which %s', $this->binaryName));
        $p->run();
        $output = trim($p->getOutput());
        if ('' === $output) {
            throw new \RuntimeException('binary path not found. Is Pygments installed?');
        }

        return $output;
    }

    /**
     * execute a call to pygmentize binary
     *
     * @param Pygmentize $pygmentize
     * @param array $options
     *
     * @return string
     */
    public function execute(Pygmentize $pygmentize, array $options)
    {
        $cmd = sprintf('-f %s -l %s -O encoding=%s %s %s',
            $pygmentize->getFormat(),
            $pygmentize->getLexer(),
            mb_detect_encoding(file_get_contents($pygmentize->getSubject())),
            $this->generateOptions($options),
            $pygmentize->getSubject()
        );

        return $this->executeCommand($cmd);
    }

    /**
     * @param array $options
     * @return string
     */
    private function generateOptions(array $options)
    {
        $output = '';
        foreach ($options as $name => $value) {
            $output .= sprintf(' -P %s=%s', $name, $value);
        }
        return $output;
    }

    /**
     * executes a command
     *
     * @param string $command command
     *
     * @return string
     * @throws \RuntimeException
     */
    public function executeCommand($command)
    {
        $cmd = sprintf('%s %s', $this->binaryPath, $command);
        $p = new Process($cmd);
        $p->run();
        if ($p->isSuccessful()) {
            return $p->getOutput();
        } else {
            throw new \RuntimeException(sprintf('pygmentize failed with the error: %s', $p->getErrorOutput()));
        }
    }

    /**
     * guess the lexer from the filename
     *
     * @param string $filename file name
     *
     * @return string
     */
    public function guessLexer($filename)
    {
        $cmd = sprintf('%s -N %s', $this->binaryPath, $filename);
        $p = new Process($cmd);
        $p->run();
        $output = trim($p->getOutput());
        if ('text' === $output) {
            $output = $this->guessFromExtension($filename);
        }
        if ('text' === $output) {
            $output = $this->guessFromContent($filename);
        }

        return $output;
    }

    /**
     * try to guess lexer from file extension (for unknown extensions to pygments)
     *
     * @param string $filename file name
     *
     * @return string
     */
    private function guessFromExtension($filename)
    {
        $output = 'text';
        $pInfo = pathinfo($filename);
        if (isset($pInfo['extension'])) {
            foreach ($this->fileTypes as $extension => $lexer) {
                if ($extension === $pInfo['extension']) {
                    return $lexer;
                }
            }
        }

        return $output;
    }

    /**
     * try to guess lexer from the file content, by searching some text
     *
     * @param string $filename file name
     *
     * @return string
     */
    private function guessFromContent($filename)
    {
        $output = 'text';
        if (!is_file($filename)) {
            return $output;
        }
        if ($fileContents = file_get_contents($filename)) {
            if (1 === preg_match('/<?xml.*?>/', $fileContents)) {
                $output = 'xml';
            }
        }

        return $output;
    }
}
