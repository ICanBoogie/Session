<?php

namespace ICanBoogie;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Runs the PHP examples of the README, which is the only source of truth.
 *
 * A trailing `// value` comment, when `value` is a short scalar, is verified against the value of the
 * expression on the same line (`echo` and assignments included). A failing example throws.
 */
final class ReadmeTest extends TestCase
{
    private const INI_KEYS = [ 'session.name', 'session.cache_limiter', 'session.use_cookies' ];

    /**
     * @var array<string, string|false>
     */
    private array $ini = [];

    /**
     * @return array<string, array{ string }>
     */
    public static function provide_example(): array
    {
        $readme = (string) file_get_contents(__DIR__ . '/../README.md');

        preg_match_all('/^```php\n(.*?)^```$/ms', $readme, $matches);

        $examples = [];

        foreach ($matches[1] as $i => $code) {
            $examples[sprintf('#%d %s', $i + 1, self::title($code))] = [ $code ];
        }

        return $examples;
    }

    private static function title(string $code): string
    {
        foreach (explode("\n", $code) as $line) {
            $line = trim($line);

            if ($line !== '' && $line !== '<?php' && !str_starts_with($line, 'use ')) {
                return substr($line, 0, 40);
            }
        }

        return '';
    }

    protected function setUp(): void
    {
        $_SESSION = [];

        // Examples change the PHP config, which must not leak into other tests.
        foreach (self::INI_KEYS as $key) {
            $this->ini[$key] = ini_get($key);
        }
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_abort();
        }

        $_SESSION = [];

        foreach ($this->ini as $key => $value) {
            ini_set($key, (string) $value);
        }

        session_name($this->ini['session.name'] ?: 'PHPSESSID');
    }

    #[DataProvider('provide_example')]
    public function test_example(string $code): void
    {
        $file = (string) tempnam(sys_get_temp_dir(), 'readme');
        file_put_contents($file, self::instrument($code));

        try {
            $this->assertNotFalse($this->run_example($file));
        } finally {
            unlink($file);
        }
    }

    /**
     * Runs the example with the context the README documents (`@var` tags): a session, and a valid unsafe
     * request.
     */
    private function run_example(string $file): mixed
    {
        $session = new Session();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_session_token'] = $session->token;

        ob_start();

        try {
            return (static fn(Session $session): mixed => include $file)($session);
        } finally {
            ob_end_clean();
        }
    }

    /**
     * Turns trailing `// value` comments into assertions.
     */
    private static function instrument(string $code): string
    {
        $pattern = '/^(\s*)(echo\s+)?(\$\w+\s*=(?!=)\s*)?(.+?);[ \t]*\/\/[ \t]*(\w{1,16})[ \t]*$/';
        $lines = [];

        foreach (explode("\n", $code) as $line) {
            if (!preg_match($pattern, $line, $m)) {
                $lines[] = $line;

                continue;
            }

            [ , $indent, $echo, $assignment, $expression, $expected ] = $m;

            if ($assignment) {
                $lines[] = "$indent$assignment$expression;";
                $expression = trim(rtrim($assignment, ' ='));
            }

            $lines[] = "$indent\\" . self::class . "::expect($expression, '$expected');";
        }

        return implode("\n", $lines);
    }

    public static function expect(mixed $actual, string $expected): void
    {
        $actual = is_bool($actual) ? ($actual ? 'true' : 'false') : (is_scalar($actual) ? (string) $actual : '');

        self::assertSame($expected, $actual);
    }
}
