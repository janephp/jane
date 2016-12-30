<?php

namespace Joli\Jane\JsonReference;

use League\Uri\Schemes\Generic\AbstractUri;
use League\Uri\Schemes\Http;
use League\Uri\UriParser;
use Rs\Json\Pointer;

/**
 * A json reference
 */
class Reference
{
    /**
     * @var mixed Cache of the resolved value
     */
    private $resolved;

    /**
     * @var AbstractUri Parse valued of the reference
     */
    private $referenceUri;

    /**
     * @var AbstractUri Parsed value of the origin of the document containing this reference
     */
    private $originUri;

    /**
     * @var AbstractUri Parsed value of the combined path between the origin and the reference
     */
    private $mergedUri;

    /**
     * @param string $reference The value of the reference
     * @param string $origin    The origin of the document containing this reference (to deal with relative path)
     */
    public function __construct($reference, $origin)
    {
        $uriParse = new UriParser();

        $originParts = $uriParse->parse($origin);
        $referenceParts = parse_url($reference);
        $mergedParts = array_merge($originParts, $referenceParts);

        if (array_key_exists('path', $referenceParts)) {
            $mergedParts['path'] = $this->joinPath(dirname($originParts['path']), $referenceParts['path']);
        }

        $this->referenceUri = Http::createFromString($reference);
        $this->originUri = Http::createFromString($origin);
        $this->mergedUri = Http::createFromComponents($mergedParts);
    }

    /**
     * Resolve a JSON Reference
     *
     * @param callable|null $deserializeCallback
     *
     * @return mixed Return the json value (deserialized) referenced
     */
    public function resolve(callable $deserializeCallback = null)
    {
        if (null === $deserializeCallback) {
            $deserializeCallback = function ($data) { return $data; };
        }

        if ($this->resolved === null) {
            $this->resolved = $this->doResolve();
        }

        return $deserializeCallback($this->resolved);
    }

    /**
     * Resolve a JSON Reference for a Schema
     *
     * @return mixed Return the json value referenced
     */
    protected function doResolve()
    {
        $json = file_get_contents((string) $this->mergedUri->withFragment(''));
        $pointer = new Pointer($json);

        if ($this->mergedUri->getFragment() === '') {
            return json_decode($json);
        }

        return $pointer->get($this->mergedUri->getFragment());
    }

    /**
     * Return true if reference and origin are in the same document
     *
     * @return bool
     */
    public function isInCurrentDocument()
    {
        return (
            $this->mergedUri->getScheme() === $this->originUri->getScheme()
            &&
            $this->mergedUri->getHost() === $this->originUri->getHost()
            &&
            $this->mergedUri->getPort() === $this->originUri->getPort()
            &&
            $this->mergedUri->getPath() === $this->originUri->getPath()
            &&
            $this->mergedUri->getQuery() === $this->originUri->getQuery()
        );
    }

    /**
     * @return AbstractUri
     */
    public function getMergedUri()
    {
        return $this->mergedUri;
    }

    /**
     * @return AbstractUri
     */
    public function getReferenceUri()
    {
        return $this->referenceUri;
    }

    /**
     * @return AbstractUri
     */
    public function getOriginUri()
    {
        return $this->originUri;
    }

    /**
     * Join path like unix path join :
     *
     *   a/b + c => a/b/c
     *   a/b + /c => /c
     *   a/b/c + .././d => a/b/d
     *
     * @param array ...$paths
     *
     * @return string
     */
    private function joinPath(...$paths)
    {
        $resultPath = null;

        foreach($paths as $path) {
            if ($resultPath === null || (!empty($path) && $path[0] === '/')) {
                $resultPath = $path;
            } else {
                $resultPath = $resultPath . '/' . $path;
            }
        }

        $resultPath = preg_replace('~/{2,}~','/', $resultPath);

        if ($resultPath === '/') {
            return '/';
        }

        $resultPathParts = [];
        foreach(explode('/',rtrim($resultPath,'/')) as $part) {
            if ('.' === $part) {
                continue;
            }

            if ('..' === $part && count($resultPathParts) > 0) {
                array_pop($resultPathParts);
                continue;
            }

            $resultPathParts[] = $part;
        }

        return implode('/',$resultPathParts);
    }
}
