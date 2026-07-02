<?php

namespace yiiunit\extensions\swiftmailer;

use Yii;
use yii\swiftmailer\Logger;

class LoggerTest extends TestCase
{
    protected function getLastLogMessage()
    {
        return end(Yii::getLogger()->messages);
    }

    /**
     * Data provider for [[testAdd()]]
     * @return array test data
     */
    public function dataProviderAdd()
    {
        return [
            [
                '>> command sent',
                [
                    'message' => '>> command sent',
                    'level' => \yii\log\Logger::LEVEL_INFO,
                ]
            ],
            [
                '<< response received',
                [
                    'message' => '<< response received',
                    'level' => \yii\log\Logger::LEVEL_INFO,
                ]
            ],
            [
                '++ transport started',
                [
                    'message' => '++ transport started',
                    'level' => \yii\log\Logger::LEVEL_TRACE,
                ]
            ],
            [
                '!! error message',
                [
                    'message' => '!! error message',
                    'level' => \yii\log\Logger::LEVEL_WARNING,
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProviderAdd
     *
     * @param string $entry
     * @param array $expectedLogMessage
     */
    public function testAdd($entry, array $expectedLogMessage)
    {
        $logger = new Logger();

        $logger->add($entry);

        $logMessage = $this->getLastLogMessage();

        $this->assertEquals($expectedLogMessage['message'], $logMessage[0]);
        $this->assertEquals($expectedLogMessage['level'], $logMessage[1]);
    }
}