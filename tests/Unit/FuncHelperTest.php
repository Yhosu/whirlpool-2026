<?php

namespace Tests\Unit;

use App\Helpers\Func;
use PHPUnit\Framework\TestCase;

class FuncHelperTest extends TestCase
{
    public function test_generate_raw_code_numeric_always_starts_with_one(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $code = Func::generateRawCode(6, 'number');
            $this->assertSame(6, strlen($code));
            $this->assertStringStartsWith('1', $code);
            $this->assertMatchesRegularExpression('/^1[0-9]{5}$/', $code);
        }
    }

    public function test_generate_raw_code_alphanumeric_always_starts_with_one(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $code = Func::generateRawCode(6, 'alphanumeric');
            $this->assertSame(6, strlen($code));
            $this->assertStringStartsWith('1', $code);
            $this->assertMatchesRegularExpression('/^1[A-Z0-9]{5}$/', $code);
        }
    }

    public function test_generate_raw_code_eight_digits(): void
    {
        $code = Func::generateRawCode(8, 'number');
        $this->assertSame(8, strlen($code));
        $this->assertStringStartsWith('1', $code);
        $this->assertMatchesRegularExpression('/^1[0-9]{7}$/', $code);
    }

    public function test_generate_raw_code_edge_cases(): void
    {
        $this->assertSame('1', Func::generateRawCode(1, 'number'));
        $this->assertSame('1', Func::generateRawCode(1, 'alphanumeric'));
        $this->assertSame('', Func::generateRawCode(0, 'number'));
    }
}
