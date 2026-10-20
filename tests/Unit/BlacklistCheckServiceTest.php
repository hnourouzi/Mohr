<?php

namespace Tests\Unit;

use App\Enums\BlacklistSeverity;
use App\Models\BlacklistWord;
use App\Services\Blacklist\BlacklistCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlacklistCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    private BlacklistCheckService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BlacklistCheckService::class);
    }

    public function test_no_match_passes(): void
    {
        $result = $this->service->check('متن سالم و بی‌خطر');

        $this->assertTrue($result->passed);
        $this->assertNull($result->severity);
    }

    public function test_block_match_fails_with_block_severity(): void
    {
        BlacklistWord::create(['word' => 'ممنوع', 'severity' => BlacklistSeverity::Block]);

        $result = $this->service->check('این متن شامل ممنوع است');

        $this->assertFalse($result->passed);
        $this->assertSame(BlacklistSeverity::Block, $result->severity);
    }

    public function test_flag_match_without_block(): void
    {
        BlacklistWord::create(['word' => 'مشکوک', 'severity' => BlacklistSeverity::Flag]);

        $result = $this->service->check('این مشکوک به نظر می‌رسد');

        $this->assertFalse($result->passed);
        $this->assertSame(BlacklistSeverity::Flag, $result->severity);
    }

    public function test_block_takes_precedence_over_flag(): void
    {
        BlacklistWord::create(['word' => 'مشکوک', 'severity' => BlacklistSeverity::Flag]);
        BlacklistWord::create(['word' => 'ممنوع', 'severity' => BlacklistSeverity::Block]);

        $result = $this->service->check('مشکوک و ممنوع با هم');

        $this->assertSame(BlacklistSeverity::Block, $result->severity);
        $this->assertSame('ممنوع', $result->matchedWord);
    }

    public function test_case_insensitive_unicode_match(): void
    {
        BlacklistWord::create(['word' => 'Bad', 'severity' => BlacklistSeverity::Block]);

        $result = $this->service->check('this is BAD word');

        $this->assertFalse($result->passed);
    }

    public function test_single_letter_blacklist_does_not_match_inside_larger_word(): void
    {
        BlacklistWord::create(['word' => 'a', 'severity' => BlacklistSeverity::Block]);

        $result = $this->service->check('veritatis');

        $this->assertTrue($result->passed);
    }

    public function test_single_letter_blacklist_matches_standalone_token(): void
    {
        BlacklistWord::create(['word' => 'a', 'severity' => BlacklistSeverity::Block]);

        $result = $this->service->check('grade a student');

        $this->assertFalse($result->passed);
        $this->assertSame('a', $result->matchedWord);
    }
}
