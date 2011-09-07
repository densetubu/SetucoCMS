<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * This class provides an interface to a single source file.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 0.10.5
 * @link       http://pdepend.org/
 */
class PHP_Depend_Code_File implements PHP_Depend_Code_NodeI
{
    /**
     * The type of this class.
     * 
     * @since 0.10.0
     */
    const CLAZZ = __CLASS__;

    /**
     * The internal used cache instance.
     *
     * @var PHP_Depend_Util_Cache_Driver
     * @since 0.10.0
     */
    protected $cache = null;

    /**
     * The unique identifier for this function.
     *
     * @var string
     */
    protected $uuid = null;

    /**
     * The source file name/path.
     *
     * @var string
     */
    protected $fileName = null;

    /**
     * The comment for this type.
     *
     * @var string
     */
    protected $docComment = null;

    /**
     * The files start line. This property must always have the value <em>1</em>.
     *
     * @var integer
     * @since 0.10.0
     */
    protected $startLine = 0;

    /**
     * The files end line.
     *
     * @var integer
     * @since 0.10.0
     */
    protected $endLine = 0;

    /**
     * List of classes, interfaces and functions that parsed from this file.
     *
     * @var array(PHP_Depend_Code_AbstractItem)
     * @since 0.10.0
     */
    protected $childNodes = array();

    /**
     * Was this file instance restored from the cache?
     *
     * @var boolean
     * @since 0.10.0
     */
    protected $cached = false;

    /**
     * Normalized code in this file.
     *
     * @var string $_source
     */
    private $_source = null;

    /**
     * Constructs a new source file instance.
     *
     * @param string $fileName The source file name/path.
     */
    public function __construct($fileName)
    {
        if ($fileName !== null) {
            $this->fileName = realpath($fileName);
        }
    }

    /**
     * Returns the physical file name for this object.
     *
     * @return string
     */
    public function getName()
    {
        return $this->fileName;
    }

    /**
     * Returns the physical file name for this object.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Returns a uuid for this code node.
     *
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * Sets the unique identifier for this file instance.
     *
     * @param string $uuid Identifier for this file.
     *
     * @return void
     * @since 0.9.12
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Setter method for the used parser and token cache.
     *
     * @param PHP_Depend_Util_Cache_Driver $cache A cache driver instance.
     *
     * @return PHP_Depend_Code_File
     * @since 0.10.0
     */
    public function setCache(PHP_Depend_Util_Cache_Driver $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Returns normalized source code with stripped whitespaces.
     *
     * @return array(integer=>string)
     */
    public function getSource()
    {
        $this->readSource();
        return $this->_source;
    }

    /**
     * Returns an <b>array</b> with all tokens within this file.
     *
     * @return array(array)
     */
    public function getTokens()
    {
        return (array) $this->cache
            ->type('tokens')
            ->restore($this->getUUID());
    }

    /**
     * Sets the tokens for this file.
     *
     * @param array(array) $tokens The generated tokens.
     *
     * @return void
     */
    public function setTokens(array $tokens)
    {
        return $this->cache
            ->type('tokens')
            ->store($this->getUUID(), $tokens);
    }

    /**
     * Returns the doc comment for this item or <b>null</b>.
     *
     * @return string
     */
    public function getDocComment()
    {
        return $this->docComment;
    }

    /**
     * Sets the doc comment for this item.
     *
     * @param string $docComment The doc comment block.
     *
     * @return void
     */
    public function setDocComment($docComment)
    {
        $this->docComment = $docComment;
    }

    /**
     * Adds a source item that was parsed from this source file.
     *
     * @param PHP_Depend_Code_AbstractItem $item Node parsed in this file.
     *
     * @return void
     * @since 0.10.0
     */
    public function addChild(PHP_Depend_Code_AbstractItem $item)
    {
        $this->childNodes[$item->getUUID()] = $item;
    }

    /**
     * Returns the start line number for this source file. For an existing file
     * this value must always be <em>1</em>, while it can be <em>0</em> for a
     * not existing dummy file.
     *
     * @return integer
     * @since 0.10.0
     */
    public function getStartLine()
    {
        if ($this->startLine === 0) {
            $this->readSource();
        }
        return $this->startLine;
    }

    /**
     * Returns the start line number for this source file. For an existing file
     * this value must always be greater <em>0</em>, while it can be <em>0</em>
     * for a not existing dummy file.
     *
     * @return integer
     * @since 0.10.0
     */
    public function getEndLine()
    {
        if ($this->endLine === 0) {
            $this->readSource();
        }
        return $this->endLine;
    }

    /**
     * This method will return <b>true</b> when this file instance was restored
     * from the cache and not currently parsed. Otherwise this method will return
     * <b>false</b>.
     *
     * @return boolean
     * @since 0.10.0
     */
    public function isCached()
    {
        return $this->cached;
    }

    /**
     * Visitor method for node tree traversal.
     *
     * @param PHP_Depend_VisitorI $visitor The context visitor
     *                                              implementation.
     *
     * @return void
     */
    public function accept(PHP_Depend_VisitorI $visitor)
    {
        $visitor->visitFile($this);
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before it serializes an instance of this class. This method returns an
     * array with those property names that should be serialized.
     *
     * @return array(string)
     * @since 0.10.0
     */
    public function __sleep()
    {
        return array(
            'cache',
            'childNodes',
            'docComment',
            'endLine',
            'fileName',
            'startLine',
            'uuid'
        );
    }

    /**
     * The magic wakeup method will is called by PHP's runtime environment when
     * a serialized instance of this class was unserialized. This implementation
     * of the wakeup method restores the references between all parsed entities
     * in this source file and this file instance.
     *
     * @return void
     * @since 0.10.0
     * @see PHP_Depend_Code_File::$childNodes
     */
    public function __wakeup()
    {
        $this->cached = true;

        foreach ($this->childNodes as $childNode) {
            $childNode->setSourceFile($this);
        }
    }

    /**
     * Returns the string representation of this class.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->fileName === null ? '' : $this->fileName);
    }

    /**
     * Reads the source file if required.
     *
     * @return void
     */
    protected function readSource()
    {
        if ($this->_source === null && file_exists($this->fileName)) {
            $source = file_get_contents($this->fileName);

            $this->_source = str_replace(array("\r\n", "\r"), "\n", $source);

            $this->startLine = 1;
            $this->endLine   = substr_count($this->_source, "\n") + 1;
        }
    }
    
    // Deprecated methods
    // @codeCoverageIgnoreStart

    /**
     * This method can be called by the PHP_Depend runtime environment or a
     * utilizing component to free up memory. This methods are required for
     * PHP version < 5.3 where cyclic references can not be resolved
     * automatically by PHP's garbage collector.
     *
     * @return void
     * @since 0.9.12
     * @deprecated Since 0.10.0
     */
    public function free()
    {
        fwrite(STDERR, __METHOD__ . ' is deprecated since version 0.10.0' . PHP_EOL);
    }

    // @codeCoverageIgnoreEnd
}
