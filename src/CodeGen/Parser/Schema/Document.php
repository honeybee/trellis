<?php

namespace Trellis\CodeGen\Parser\Schema;

use DOMDocument;
use DOMAttr;
use DOMException;
use LibXMLError;

/**
 * The Xpath class is a conveniece wrapper around DOMDocument to:
 *      - support xinclude from another URI, without adding 'xml:base' attribute into the schema
 *      - handle libxmls error as PHP exceptions
 */
class Document extends DOMDocument
{
    public function xinclude($options = null)
    {
        $user_error_handling = $this->enableErrorHandling();

        $return = parent::xinclude($options);

        $this->handleErrors(
            'Resolving XIncludes failed. Details are:' . PHP_EOL . PHP_EOL,
            PHP_EOL . 'Please fix the mentioned errors.',
            $user_error_handling
        );

        // Remove xml:base attribute, auto-appended when xincluding resources with different URI
        $xpath = new Xpath($this);

        $nodes = $xpath->query('//@xml:base', $this);
        foreach ($nodes as $node) {
            $node->ownerElement->removeAttribute($node->nodeName);
        }
        return $return;
    }

    public function load($filename, $options = 0)
    {
        $user_error_handling = $this->enableErrorHandling();

        $return = parent::load($filename, $options);

        $this->handleErrors(
            'Loading XML document failed. Details are:' . PHP_EOL . PHP_EOL,
            PHP_EOL . 'Please fix the mentioned errors.',
            $user_error_handling
        );

        return $return;
    }

    public function schemaValidate($filename)
    {
        $user_error_handling = $this->enableErrorHandling();

        $return = parent::schemaValidate($filename);

        $this->handleErrors(
            'Schema validation failed. Details are:' . PHP_EOL . PHP_EOL,
            PHP_EOL . 'Please fix the mentioned errors.',
            $user_error_handling
        );

        return $return;
    }

    /**
     * Disables libxml errors, allowing the parser to take care of the errors by itself.
     *
     * @return bool The former "use_errors" value.
     */
    public function enableErrorHandling()
    {
        $user_error_handling = libxml_use_internal_errors(true);
        libxml_clear_errors();

        return $user_error_handling;
    }

    /**
     * Checks for internal libxml errors and throws them in form of a single DOMException.
     * If no errors occured, then nothing happens.
     *
     * @param string $msg_prefix Is prepended to the libxml error message.
     * @param string $msg_suffix Is appended to the libxml error message.
     * @param bool $user_error_handling Allows to enable or disable internal libxml error handling.
     *
     * @throws DOMException
     */
    public function handleErrors($msg_prefix = '', $msg_suffix = '', $user_error_handling = false)
    {
        if (libxml_get_last_error() !== false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            libxml_use_internal_errors($user_error_handling);

            throw new DOMException(
                $msg_prefix .
                $this->getErrorMessage($errors) .
                $msg_suffix
            );
        }

        libxml_use_internal_errors($user_error_handling);
    }

    /**
     * Converts a given list of libxml errors into an error report.
     *
     * @param array $errors
     *
     * @return string
     */
    protected function getErrorMessage(array $errors)
    {
        $error_message = '';
        foreach ($errors as $error) {
            $error_message .= $this->parseError($error) . PHP_EOL . PHP_EOL;
        }

        return $error_message;
    }

    /**
     * Converts a given libxml error into an error message.
     *
     * @param LibXMLError $error
     *
     * @return string
     */
    protected function parseError(LibXMLError $error)
    {
        $prefix_map = [
            LIBXML_ERR_WARNING => '[warning]',
            LIBXML_ERR_FATAL => '[fatal]',
            LIBXML_ERR_ERROR => '[error]'
        ];
        $prefix = isset($prefix_map[$error->level]) ? $prefix_map[$error->level] : $prefix_map[LIBXML_ERR_ERROR];

        $msg_parts = [];
        $msg_parts[] = sprintf('%s %s: %s', $prefix, $error->level, trim($error->message));
        $msg_parts[] = sprintf('Line: %d', $error->line);
        $msg_parts[] = sprintf('Column: %d', $error->column);
        if ($error->file) {
            $msg_parts[] = sprintf('File: %s', $error->file);
        }

        return implode(PHP_EOL, $msg_parts);
    }
}
