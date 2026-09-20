<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\WebService;
use WebFiori\Http\OpenAPI\OpenAPIGenerator;
use WebFiori\Tests\Http\TestServices\AllMethodsService;
use WebFiori\Tests\Http\TestServices\ParameterSetService;

/**
 * Regression tests for the per-class annotation caching (issue #154).
 *
 * The cache must never change observable behavior: repeated constructions of a
 * class (cache hit) must produce state identical to the first construction
 * (cache miss), per-instance names must stay independent, and OpenAPI output
 * must be unchanged.
 */
class AnnotationCacheTest extends TestCase {
    private function snapshot(WebService $s): array {
        return [
            'name' => $s->getName(),
            'path' => $s->getPath(),
            'description' => $s->getDescription(),
            'methods' => $s->getRequestMethods(),
            'authRequired' => $s->isAuthRequired(),
            'params' => array_keys($s->getParameters()),
        ];
    }

    /**
     * Cache miss (first) vs cache hit (subsequent) produce identical derived state.
     */
    public function testAnnotatedServiceMissEqualsHit() {
        $first = $this->snapshot(new AllMethodsService());  // may be a miss
        $second = $this->snapshot(new AllMethodsService()); // guaranteed hit
        $third = $this->snapshot(new AllMethodsService());  // guaranteed hit

        $this->assertSame($first, $second);
        $this->assertSame($second, $third);
        $this->assertSame('all-methods', $first['name']);
        $this->assertSame(['GET', 'POST', 'PUT', 'DELETE'], $first['methods']);
    }

    /**
     * Parameter configuration (per-method reflection memoization) is identical
     * across instances, and produces independent parameter objects.
     */
    public function testParameterServiceMissEqualsHit() {
        $a = new ParameterSetService();
        $b = new ParameterSetService();

        $this->assertSame(
            array_keys($a->getParameters()),
            array_keys($b->getParameters())
        );

        // Independent objects — mutating one instance's params must not affect
        // the other (guards against shared cached objects leaking into OpenAPI).
        if (!empty($a->getParameters())) {
            $this->assertNotSame(
                array_values($a->getParameters())[0],
                array_values($b->getParameters())[0]
            );
        }
    }

    /**
     * The correctness landmine: constructing the same (unannotated) class with
     * different names must keep the names independent — the name is never cached.
     */
    public function testUnannotatedNamesRemainIndependent() {
        $a = new WebService('users');
        $b = new WebService('orders');
        $c = new WebService('users');

        $this->assertSame('users', $a->getName());
        $this->assertSame('orders', $b->getName());
        $this->assertSame('users', $c->getName());
    }

    /**
     * OpenAPI generation is unaffected by caching: the same service class
     * produces identical path-item output across instances.
     */
    public function testOpenApiOutputStableAcrossInstances() {
        $gen = new OpenAPIGenerator();

        $spec1 = $gen->generate([new AllMethodsService()])->toJSON();
        $spec2 = $gen->generate([new AllMethodsService()])->toJSON();

        $this->assertSame((string) $spec1, (string) $spec2);
    }
}
