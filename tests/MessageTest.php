<?php

namespace yiiunit\extensions\swiftmailer;

use Yii;
use yii\helpers\FileHelper;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

Yii::setAlias('@yii/swiftmailer', __DIR__ . '/../../../../extensions/swiftmailer');

/**
 * @group vendor
 * @group mail
 * @group swiftmailer
 */
class MessageTest extends TestCase
{

    /**
     * @var string test email address, which will be used as receiver for the messages.
     */
    protected $testEmailReceiver = 'someuser@somedomain.com';
    /**
     * @var string test private key for DKIM signing
     */
    protected $fakeKey = "-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAyehiMTRxvfQz8nbQQAgL481QipVMF+E7ljWKHTQQSYfqktR+
zFYqX81vKeK9/2D6AiK5KJSBVdF7aURasppuDaxFJWrPvacd3IQCrGxsGkwwlWPO
ggB1WpOEKhVUZnGzdm96Fk23oHFKrEiQlSG0cB9P/wUKz57b8tsaPve5sKBG0Kww
9YIDRM0x4w3c9fupPz8H5p2HHn4uPbn+whJyALZHD1+CftIGOHq8AUH4w4Z7bjF4
DD4zibpgRn96BVaRIJjxZdlKq69v52j3v8O8SAqSkWmpDWiIsm85Gl00Loay6iiJ
XNy11y0sUysFeCSpb/9cRyxb6j0jEwQXrw0J/QIDAQABAoIBAQCFuRgXeKGAalVh
V5mTXwDo7hlSv5C3HCBH2svPjaTf3lnYx033bXYBH2Fpf1fQ5NyQP4kcPEbwnJ48
2N2s/qS2/4qIPpa6CA259+CBbAmo3R8sQf8KkN0okRzudlQAyXtPjINydCSS6ZXI
RwMjEkCcJdDomOFRIuiPjtdyLsXYGRAa95yjpTU0ri1mEJocX6tlchlgUsjwc2ml
rCTKLc6b3KtYNYUZ/Rg0HzWRIhkbQFIz7uS0t7gF3sqDOLcaoWIv2rmrpg5T0suA
e5Sz7nK2XBeaPi/AKNCVoXJiCJ6SU6A+6Q4T5Rvnt+uxGpLKiilb/fRpQaq1RFO9
k5BDPgftAoGBAPyYBPrTPYPYGosqzbFypNaWLOUnjkdFxlThpwvLOa7nzwVcsQ8V
EXDkELNYy/jOYJLsNhhZ+bGAwWdNV46pdurFKuzS4vb11RfZCc3BTM05IFUFrKir
YVgWw5AYKJLkUiACASEP55P8j2cKocCV5SdI0sGyU7W+3S1NbhBOlr0nAoGBAMyh
Y/Ki5wo3LX43l9F1I2HnKVJSj2XzpWTSYco8sUbS4yUBVk9qPBjIHhT+mK2k2FqD
bSWsu5tGVfaMlFbYxXnSBqjIQfHRLWWVmWMr5sLFk0aJyY1mjGh6BEhTp/Xs86/w
cdVlI1N5blxPy4VvoLmHIb/O1xqi64FV1gW7gD47AoGAErFlXPKZENLDVB08z67+
R+shM2wz+U5OmSWB6TuG70y0Y18ysz0J52LZYYxmu+j5+KWGc1LlSZ+PsIdmvWYJ
KOKihJgut7wFoxgqw5FUj7N0kxYyauET+SLmIhnHludStI+xabL1nlwIeMWupsPx
C3E2N6Ns0nxnfdzHEmneee0CgYA5kF0RcIoV8Ze2neTzY0Rk0iZpphf40iWAyz3/
KjukdMa5LjsddAEb54+u0EAa+Phz3eziYEkWUR71kG5aT/idYFvHNy513CYtIXxY
zYzI1dOsUC6GvIZbDZgO0Jm7MMEMiVM8eIsLfGlzRm82RkSsbDsuPf183L/rTj46
tphI6QKBgQDobarzJhVUdME4QKAlhJecKBO1xlVCXWbKGdRcJn0Gzq6iwZKdx64C
hQGpKaZBDDCHLk7dDzoKXF1udriW9EcImh09uIKGYYWS8poy8NUzmZ3fy/1o2C2O
U41eAdnQ3dDGzUNedIJkSh6Z0A4VMZIEOag9hPNYqQXZBQgfobvPKw==
-----END RSA PRIVATE KEY-----
";

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'mailer' => $this->createTestEmailComponent()
            ]
        ]);
        $filePath = $this->getTestFilePath();
        if (!file_exists($filePath)) {
            FileHelper::createDirectory($filePath);
        }
    }

    public function tearDown()
    {
        $filePath = $this->getTestFilePath();
        if (file_exists($filePath)) {
            FileHelper::removeDirectory($filePath);
        }
    }

    /**
     * @return string test file path.
     */
    protected function getTestFilePath()
    {
        return Yii::getAlias('@yiiunit/extensions/swiftmailer/runtime') . DIRECTORY_SEPARATOR . basename(get_class($this)) . '_' . getmypid();
    }

    /**
     * @return Mailer test email component instance.
     */
    protected function createTestEmailComponent()
    {
        $component = new Mailer([
            'useFileTransport' => true,
        ]);

        return $component;
    }

    /**
     * @return Message test message instance.
     */
    protected function createTestMessage()
    {
        return Yii::$app->get('mailer')->compose();
    }

    /**
     * Creates image file with given text.
     * @param  string $fileName file name.
     * @param  string $text     text to be applied on image.
     * @return string image file full name.
     */
    protected function createImageFile($fileName = 'test.jpg', $text = 'Test Image')
    {
        if (!function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD lib required.');
        }
        $fileFullName = $this->getTestFilePath() . DIRECTORY_SEPARATOR . $fileName;
        $image = imagecreatetruecolor(120, 20);
        $textColor = imagecolorallocate($image, 233, 14, 91);
        imagestring($image, 1, 5, 5, $text, $textColor);
        imagejpeg($image, $fileFullName);
        imagedestroy($image);

        return $fileFullName;
    }

    /**
     * Finds the attachment object in the message.
     * @param  Message                     $message message instance
     * @return null|\Swift_Mime_Attachment attachment instance.
     */
    protected function getAttachment(Message $message)
    {
        $messageParts = $message->getSwiftMessage()->getChildren();
        $attachment = null;
        foreach ($messageParts as $part) {
            if ($part instanceof \Swift_Mime_Attachment) {
                $attachment = $part;
                break;
            }
        }

        return $attachment;
    }

    /**
     * Returns a test message including from,to, subject and textbody
     * @return Message test message 
     * @see 'testSend()'
     * @see 'testSendSigned()'
     */
    protected function getTestMessage()
    {
        $message = $this->createTestMessage();
        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Test');
        $message->setTextBody('Yii Swift Test body');
        return $message;
    }

// Tests :

    public function testGetSwiftMessage()
    {
        $message = new Message();
        $swiftMessage = $message->getSwiftMessage();
        $this->assertTrue(is_object($swiftMessage), 'Unable to get Swift message!');
        $this->assertInstanceOf("\Swift_Message", $swiftMessage, 'getSwiftMessage not returning \Swift_Message when expected');
    }

    public function testGetDkimSigner()
    {
        $message = new Message;
        $this->assertInstanceOf("\Swift_Signers_DKIMSigner", $message->getDkimSigner(null, null, null), "getDkimSigner not returning \Swift_Signers_DKIMSigner when expected");
    }

    /**
     * @depends testGetSwiftMessage
     */
    public function testSetGet()
    {
        $message = new Message();

        $charset = 'utf-16';
        $message->setCharset($charset);
        $this->assertEquals($charset, $message->getCharset(), 'Unable to set charset!');

        $subject = 'Test Subject';
        $message->setSubject($subject);
        $this->assertEquals($subject, $message->getSubject(), 'Unable to set subject!');

        $from = 'from@somedomain.com';
        $message->setFrom($from);
        $this->assertContains($from, array_keys($message->getFrom()), 'Unable to set from!');

        $replyTo = 'reply-to@somedomain.com';
        $message->setReplyTo($replyTo);
        $this->assertContains($replyTo, array_keys($message->getReplyTo()), 'Unable to set replyTo!');

        $to = 'someuser@somedomain.com';
        $message->setTo($to);
        $this->assertContains($to, array_keys($message->getTo()), 'Unable to set to!');

        $cc = 'ccuser@somedomain.com';
        $message->setCc($cc);
        $this->assertContains($cc, array_keys($message->getCc()), 'Unable to set cc!');

        $bcc = 'bccuser@somedomain.com';
        $message->setBcc($bcc);
        $this->assertContains($bcc, array_keys($message->getBcc()), 'Unable to set bcc!');
    }

    /**
     * @depends testGetSwiftMessage
     */
    public function testSetupHeaders()
    {
        $charset = 'utf-16';
        $subject = 'Test Subject';
        $from = 'from@somedomain.com';
        $replyTo = 'reply-to@somedomain.com';
        $to = 'someuser@somedomain.com';
        $cc = 'ccuser@somedomain.com';
        $bcc = 'bccuser@somedomain.com';

        $messageString = $this->createTestMessage()
                ->setCharset($charset)
                ->setSubject($subject)
                ->setFrom($from)
                ->setReplyTo($replyTo)
                ->setTo($to)
                ->setCc($cc)
                ->setBcc($bcc)
                ->toString();

        $this->assertContains('charset=' . $charset, $messageString, 'Incorrect charset!');
        $this->assertContains('Subject: ' . $subject, $messageString, 'Incorrect "Subject" header!');
        $this->assertContains('From: ' . $from, $messageString, 'Incorrect "From" header!');
        $this->assertContains('Reply-To: ' . $replyTo, $messageString, 'Incorrect "Reply-To" header!');
        $this->assertContains('To: ' . $to, $messageString, 'Incorrect "To" header!');
        $this->assertContains('Cc: ' . $cc, $messageString, 'Incorrect "Cc" header!');
        $this->assertContains('Bcc: ' . $bcc, $messageString, 'Incorrect "Bcc" header!');
    }

    /**
     * @depends testGetSwiftMessage
     */
    public function testSend()
    {
        $this->assertTrue($this->getTestMessage()->send());
    }

    /**
     * @depends testGetSwiftMessage
     */
    public function testSendSigned()
    {
        $message = $this->getTestMessage();
        $signer = $message->getDkimSigner($this->fakeKey, null, null);
        $message->getSwiftMessage()->attachSigner($signer);
        $this->assertTrue($message->send());
    }

    /**
     * @depends testSend
     */
    public function testAttachFile()
    {
        $message = $this->createTestMessage();

        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Attach File Test');
        $message->setTextBody('Yii Swift Attach File Test body');
        $fileName = __FILE__;
        $message->attach($fileName);

        $this->assertTrue($message->send());

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertContains($attachment->getFilename(), $fileName, 'Invalid file name!');
    }

    /**
     * @depends testSend
     */
    public function testAttachContent()
    {
        $message = $this->createTestMessage();

        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Create Attachment Test');
        $message->setTextBody('Yii Swift Create Attachment Test body');
        $fileName = 'test.txt';
        $fileContent = 'Test attachment content';
        $message->attachContent($fileContent, ['fileName' => $fileName]);

        $this->assertTrue($message->send());

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertEquals($fileName, $attachment->getFilename(), 'Invalid file name!');
    }

    /**
     * @depends testSend
     */
    public function testEmbedFile()
    {
        $fileName = $this->createImageFile('embed_file.jpg', 'Embed Image File');

        $message = $this->createTestMessage();

        $cid = $message->embed($fileName);

        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Embed File Test');
        $message->setHtmlBody('Embed image: <img src="' . $cid . '" alt="pic">');

        $this->assertTrue($message->send());

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertContains($attachment->getFilename(), $fileName, 'Invalid file name!');
    }

    /**
     * @depends testSend
     */
    public function testEmbedContent()
    {
        $fileFullName = $this->createImageFile('embed_file.jpg', 'Embed Image File');
        $message = $this->createTestMessage();

        $fileName = basename($fileFullName);
        $contentType = 'image/jpeg';
        $fileContent = file_get_contents($fileFullName);

        $cid = $message->embedContent($fileContent, ['fileName' => $fileName, 'contentType' => $contentType]);

        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Embed File Test');
        $message->setHtmlBody('Embed image: <img src="' . $cid . '" alt="pic">');

        $this->assertTrue($message->send());

        $attachment = $this->getAttachment($message);
        $this->assertTrue(is_object($attachment), 'No attachment found!');
        $this->assertEquals($fileName, $attachment->getFilename(), 'Invalid file name!');
        $this->assertEquals($contentType, $attachment->getContentType(), 'Invalid content type!');
    }

    /**
     * @depends testSend
     */
    public function testSendAlternativeBody()
    {
        $message = $this->createTestMessage();

        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Alternative Body Test');
        $message->setHtmlBody('<b>Yii Swift</b> test HTML body');
        $message->setTextBody('Yii Swift test plain text body');

        $this->assertTrue($message->send());

        $messageParts = $message->getSwiftMessage()->getChildren();
        $textPresent = false;
        $htmlPresent = false;
        foreach ($messageParts as $part) {
            if (!($part instanceof \Swift_Mime_Attachment)) {
                /* @var $part \Swift_Mime_MimePart */
                if ($part->getContentType() == 'text/plain') {
                    $textPresent = true;
                }
                if ($part->getContentType() == 'text/html') {
                    $htmlPresent = true;
                }
            }
        }
        $this->assertTrue($textPresent, 'No text!');
        $this->assertTrue($htmlPresent, 'No HTML!');
    }

    /**
     * @depends testGetSwiftMessage
     */
    public function testSerialize()
    {
        $message = $this->createTestMessage();

        $message->setTo($this->testEmailReceiver);
        $message->setFrom('someuser@somedomain.com');
        $message->setSubject('Yii Swift Alternative Body Test');
        $message->setTextBody('Yii Swift test plain text body');

        $serializedMessage = serialize($message);
        $this->assertNotEmpty($serializedMessage, 'Unable to serialize message!');

        $unserializedMessaage = unserialize($serializedMessage);
        $this->assertEquals($message, $unserializedMessaage, 'Unable to unserialize message!');
    }

    /**
     * @depends testSendAlternativeBody
     */
    public function testAlternativeBodyCharset()
    {
        $message = $this->createTestMessage();
        $charset = 'windows-1251';
        $message->setCharset($charset);

        $message->setTextBody('some text');
        $message->setHtmlBody('some html');
        $content = $message->toString();
        $this->assertEquals(2, substr_count($content, $charset), 'Wrong charset for alternative body.');

        $message->setTextBody('some text override');
        $content = $message->toString();
        $this->assertEquals(2, substr_count($content, $charset), 'Wrong charset for alternative body override.');
    }

}
