<?php

namespace Trellis\CodeGen\Parser\Schema;

use DOMDocument;
use DOMXPath;
use DOMNode;
use DOMException;
use LibXMLError;

/**
 * The Xpath class is a conveniece wrapper around DOMXpath and simple adds a namespace prefix to queries.
 */
class Xpath extends DOMXpath
{
    /**
     * @var string $default_namespace
     */
    protected $document_namespace;

    protected $namespace_prefix;

    /**
     * Creates a new xpath instance that will use the given 'namespace_prefix' when querying the given document.
     *
     * @param DOMDocument $document
     */
    public function __construct(DOMDocument $document, $namespace_prefix = null)
    {
        parent::__construct($document);

        $this->initNamespace($document, $namespace_prefix);
    }

    /**
     * Takes an xpath expression and preprends the parser's namespace prefix to each xpath segment.
     * Then it runs the namespaced expression and returns the result.
     * Example: '//state_machines/state_machine' - expands to -> '//prefix:state_machines/prefix:state_machine'
     *
     * @param string $expression Non namespaced xpath expression.
     * @param DOMNode $context Allows to pass a context node that is used for the actual xpath query.
     *
     * @return \DOMNodeList
     */
    public function query($expression, DOMNode $context = null, $register_ns = null)
    {
        if ($this->hasNamespace()) {
            // Build regular expression (naming rules @ http://www.xml.com/pub/a/2001/07/25/namingparts.html)

            // Charset for nodes name, except for the first letter (that just supports '\w')
            $charset = '[\w-.]';
            // Redefine boundaries
            $look_behind = '(?<!'.$charset.')';
            $look_ahead = '(?!'.$charset.')';
            // Exclude non-node delimiters (including namespaced nodes)
            $non_node_boundaries_left = ':\'"=><';
            $non_node_boundaries_right = ':\'"(';
            $exclude_before_node = '(?<!['.$non_node_boundaries_left.'])';
            $exclude_after_node = '(?!['.$non_node_boundaries_right.'])';

            // Retrieve all nodes (element/attribute) names that are not namespaced
            $search = [
                '~'.$exclude_before_node.$look_behind.'([a-z\*]'.$charset.'*)'.$look_ahead.$exclude_after_node.'~i'
            ];
            $replace = [ sprintf('%s:$0', $this->namespace_prefix) ];
            $expression = preg_replace($search, $replace, $expression);
        }

        $user_error_handling = $this->enableErrorHandling();
        $return = parent::query($expression, $context, $register_ns);

        $this->handleErrors(
            'XPath query failed. Details are:' . PHP_EOL . PHP_EOL,
            PHP_EOL . 'Please fix the mentioned errors.',
            $user_error_handling
        );

        return $return;
    }

    /**
     * Get the namespace of the document, if defined.
     *
     * @param DOMDocument $document     Document to query on
     *
     * @return string
     */
    protected function initNamespace(DOMDocument $document, $namespace_prefix = null)
    {
        // @todo: check for conflicting namespace prefixes
        $this->document_namespace = trim($document->documentElement->namespaceURI);
        $namespace_prefix = trim($namespace_prefix);

        if ($this->hasNamespace()) {
            $this->namespace_prefix = empty($namespace_prefix) ? $this->getDefaultNamespacePrefix() : $namespace_prefix;

            $this->registerNamespace(
                $this->namespace_prefix,
                $this->document_namespace
            );
        }
    }

    protected function hasNamespace()
    {
        return !empty($this->document_namespace);
    }

    /**
     * Returns the default namespace prefix to use when running xpath queries.
     *
     * @return string
     */
    protected function getDefaultNamespacePrefix()
    {
        return 'dt';
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
