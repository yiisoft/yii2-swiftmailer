<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\swiftmailer;

use Yii;
use yii\mail\BaseMessage;

/**
 * Message implements a message class based on SwiftMailer.
 *
 * @see http://swiftmailer.org/docs/messages.html
 * @see Mailer
 *
 * @method Mailer getMailer() returns mailer instance.
 *
 * @property \Swift_Message $swiftMessage Swift message instance. This property is read-only.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Message extends BaseMessage
{

    /**
     * @var \Swift_Message Swift message instance.
     */
    private $_swiftMessage;

    /**
     * @var string Swift message class name.
     */
    public $messageClass = "\Swift_Message";

    /**
     * @return \Swift_Message Swift message instance.
     */
    public function getSwiftMessage()
    {
        if (!is_object($this->_swiftMessage)) {
            $this->_swiftMessage = $this->createSwiftMessage();
        }

        return $this->_swiftMessage;
    }

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->getSwiftMessage()->getCharset();
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->getSwiftMessage()->setCharset($charset);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->getSwiftMessage()->getFrom();
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->getSwiftMessage()->setFrom($from);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->getSwiftMessage()->getReplyTo();
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->getSwiftMessage()->setReplyTo($replyTo);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->getSwiftMessage()->getTo();
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        $this->getSwiftMessage()->setTo($to);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->getSwiftMessage()->getCc();
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->getSwiftMessage()->setCc($cc);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->getSwiftMessage()->getBcc();
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->getSwiftMessage()->setBcc($bcc);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->getSwiftMessage()->getSubject();
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->getSwiftMessage()->setSubject($subject);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->setBody($text, 'text/plain');

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->setBody($html, 'text/html');

        return $this;
    }

    /**
     * Sets the message body.
     * If body is already set and its content type matches given one, it will
     * be overridden, if content type miss match the multipart message will be composed.
     * @param string $body body content.
     * @param string $contentType body content type.
     */
    protected function setBody($body, $contentType)
    {
        $message = $this->getSwiftMessage();
        $oldBody = $message->getBody();
        $charset = $message->getCharset();
        if (empty($oldBody)) {
            $parts = $message->getChildren();
            $partFound = false;
            foreach ($parts as $key => $part) {
                if (!($part instanceof \Swift_Mime_Attachment)) {
                    /* @var $part \Swift_Mime_MimePart */
                    if ($part->getContentType() == $contentType) {
                        $charset = $part->getCharset();
                        unset($parts[$key]);
                        $partFound = true;
                        break;
                    }
                }
            }
            if ($partFound) {
                reset($parts);
                $message->setChildren($parts);
                $message->addPart($body, $contentType, $charset);
            } else {
                $message->setBody($body, $contentType);
            }
        } else {
            $oldContentType = $message->getContentType();
            if ($oldContentType == $contentType) {
                $message->setBody($body, $contentType);
            } else {
                $message->setBody(null);
                $message->setContentType(null);
                $message->addPart($oldBody, $oldContentType, $charset);
                $message->addPart($body, $contentType, $charset);
            }
        }
    }

    /**
     * Returns a \Swift_Signers_DKIMSigner object
     * @param type $privateKey
     * @param type $domain
     * @param type $selector
     * @return \Swift_Signers_DKIMSigner
     * @since 2.0.6
     */
    public function getDkimSigner($privateKey, $domain, $selector)
    {
        return new \Swift_Signers_DKIMSigner($privateKey, $domain, $selector);
    }

    /**
     * Adds a Swift_Signers_DKIMSigners object to the Swift_SignedMessage message.
     * This requires the messageClass to be set to \Swift_SignedMessage instead of \Swift_Message.
     * messageClass can be set to the correct value via yii\swiftmailer\Mailer::$messageConfig
     * @param string $privateKeyAlias an alias pointing to the private key file.
     * @param string $domain the domain to sign with.
     * @param string $selector the dkim domain selector.
     * @see 'getDkimSigner()'
     * @since 2.0.6
     */
    public function setDkim($privateKeyAlias, $domain, $selector)
    {
        $path = Yii::getAlias($privateKeyAlias);
        $dkimSigner = $this->getDkimSigner(file_get_contents($path), $domain, $selector);
        $this->getSwiftMessage()->attachSigner($dkimSigner);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $attachment = \Swift_Attachment::fromPath($fileName);
        if (!empty($options['fileName'])) {
            $attachment->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $attachment->setContentType($options['contentType']);
        }
        $this->getSwiftMessage()->attach($attachment);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        $attachment = \Swift_Attachment::newInstance($content);
        if (!empty($options['fileName'])) {
            $attachment->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $attachment->setContentType($options['contentType']);
        }
        $this->getSwiftMessage()->attach($attachment);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $embedFile = \Swift_EmbeddedFile::fromPath($fileName);
        if (!empty($options['fileName'])) {
            $embedFile->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $embedFile->setContentType($options['contentType']);
        }

        return $this->getSwiftMessage()->embed($embedFile);
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        $embedFile = \Swift_EmbeddedFile::newInstance($content);
        if (!empty($options['fileName'])) {
            $embedFile->setFilename($options['fileName']);
        }
        if (!empty($options['contentType'])) {
            $embedFile->setContentType($options['contentType']);
        }

        return $this->getSwiftMessage()->embed($embedFile);
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return $this->getSwiftMessage()->toString();
    }

    /**
     * Creates the Swift email message instance.
     * @return \Swift_Message email message instance.
     */
    protected function createSwiftMessage()
    {
        return new $this->messageClass;
    }

}
