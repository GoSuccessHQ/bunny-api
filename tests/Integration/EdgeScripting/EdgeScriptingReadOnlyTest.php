<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Integration\EdgeScripting;

use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\EdgeScripting\Model\EdgeScript;
use GoSuccess\Bunny\EdgeScripting\Model\EdgeScriptRelease;
use GoSuccess\Bunny\EdgeScripting\Model\EdgeScriptSecret;
use GoSuccess\Bunny\Exception\NotFoundException;
use GoSuccess\Bunny\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Read-only checks of the Edge Scripting API against a real account.
 *
 * Only side-effect-free GET requests are made.
 */
#[CoversNothing]
final class EdgeScriptingReadOnlyTest extends IntegrationTestCase
{
    public function testScripts(): void
    {
        $edgeScripting = new Bunny(self::apiKey())->edgeScripting;
        $page = $edgeScripting->scripts->list(perPage: 10);

        self::assertContainsOnlyInstancesOf(EdgeScript::class, $page->items);
        self::assertCount($page->totalItems ?? 0, iterator_to_array($edgeScripting->scripts->all(), false));

        try {
            $edgeScripting->scripts->get(1);
            self::fail('Expected a NotFoundException.');
        } catch (NotFoundException $e) {
            self::assertSame('edgeScript.not_found', $e->errorKey);
        }

        if ($page->items === []) {
            self::markTestSkipped('The account has no edge scripts.');
        }

        $script = $edgeScripting->scripts->get($page->items[0]->id);
        self::assertSame($page->items[0]->id, $script->id);

        $edgeScripting->scripts->code($script->id);
        $edgeScripting->scripts->statistics($script->id);
        self::assertContainsOnlyInstancesOf(EdgeScriptSecret::class, $edgeScripting->secrets->list($script->id));
        self::assertContainsOnlyInstancesOf(EdgeScriptRelease::class, $edgeScripting->releases->list($script->id)->items);

        foreach ($script->edgeScriptVariables as $variable) {
            self::assertSame($variable->name, $edgeScripting->variables->get($script->id, $variable->id)->name);
        }
    }
}
