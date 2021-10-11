<?php
/**
 * @license MIT
 * Copyright 2021, Dustin Wilson, J. King et al.
 * See LICENSE and AUTHORS files for details
 */

declare(strict_types=1);
namespace MensBeam\HTML\DOM\TestCase;

use MensBeam\Framework\{
    Exception,
    MagicProperties
};


/** @covers \MensBeam\Framework\MagicProperties */
class TestMagicProperties extends \PHPUnit\Framework\TestCase {
    public function provideFailures(): iterable {
        $ook = new class {
            use MagicProperties;
            protected ?string $_eek = 'eek';
            protected ?string $_ook = 'ook';


            protected function __get_eek(): ?string {
                return $this->_eek;
            }

            protected function __get_ook(): ?string {
                return $this->_ook;
            }

            protected function __set_ook(?string $value): void {
                $this->_ook = $value;
            }
        };

        return [
            [ function() use($ook) {
                $ook->ack;
            }, Exception::NONEXISTENT_PROPERTY ],
            [ function() use($ook) {
                $ook->ack = 'ack';
            }, Exception::NONEXISTENT_PROPERTY ],
            [ function() use($ook) {
                $ook->eek = 'ook';
            }, Exception::READONLY_PROPERTY ],
            [ function() use($ook) {
                unset($ook->eek);
            }, Exception::READONLY_PROPERTY ]
        ];
    }

    /**
     * @dataProvider provideFailures
     * @covers \MensBeam\Framework\MagicProperties::__get
     * @covers \MensBeam\Framework\MagicProperties::__set
     * @covers \MensBeam\Framework\MagicProperties::__unset
     */
    public function testFailures(\Closure $closure, int $errorCode): void {
        $this->expectException(Exception::class);
        $this->expectExceptionCode($errorCode);
        $closure();
    }


    /** @covers \MensBeam\Framework\MagicProperties::__isset */
    public function testIsset(): void {
        $ook = new class {
            use MagicProperties;

            protected function __get_ook(): ?string {
                return 'ook';
            }
        };

        $this->assertTrue(isset($ook->ook));
    }


    /** @covers \MensBeam\Framework\MagicProperties::__unset */
    public function testUnset(): void {
        $ook = new class {
            use MagicProperties;
            protected ?string $_ook = 'ook';


            protected function __get_ook(): ?string {
                return $this->_ook;
            }

            protected function __set_ook(?string $value): void {
                $this->_ook = $value;
            }
        };

        unset($ook->ook);
        $this->assertNull($ook->ook);
    }


    /** @covers \MensBeam\Framework\MagicProperties::__set */
    public function testSet(): void {
        $ook = new class {
            use MagicProperties;
            protected ?string $_ook = 'ook';


            protected function __get_ook(): ?string {
                return $this->_ook;
            }

            protected function __set_ook(?string $value): void {
                $this->_ook = $value;
            }
        };

        $ook->ook = 'eek';
        $this->assertSame('eek', $ook->ook);
    }
}