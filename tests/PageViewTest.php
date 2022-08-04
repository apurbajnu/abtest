<?php

namespace Apurbajnu\Abtest\Tests;

use Apurbajnu\Abtest\Abtest;
use Apurbajnu\Abtest\AbtestFacade;
use Apurbajnu\Abtest\Events\ExperimentNewVisitor;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;

class PageViewTest extends TestCase
{
    public function test_that_pageview_works()
    {
        AbtestFacade::pageView();

        $experiment = session(Abtest::SESSION_KEY_EXPERIMENT);

        $this->assertEquals($this->experiments[0], $experiment->name);
        $this->assertEquals(1, $experiment->visitors);

        Event::assertDispatched(ExperimentNewVisitor::class, function ($e) use ($experiment) {
            return $e->experiment->id === $experiment->id;
        });
    }

    public function test_that_pageview_changes_after_first_test()
    {
        $this->test_that_pageview_works();

        session()->flush();

        $this->assertNull(session(Abtest::SESSION_KEY_EXPERIMENT));

        AbtestFacade::pageView();

        $experiment = session(Abtest::SESSION_KEY_EXPERIMENT);

        $this->assertEquals($this->experiments[1], $experiment->name);
        $this->assertEquals(1, $experiment->visitors);
    }

    public function test_that_pageview_does_not_trigger_for_crawlers()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'crawl';
        config()->set('ab-testing.ignore_crawlers', true);

        AbtestFacade::pageView();

        $this->assertNull(session(Abtest::SESSION_KEY_EXPERIMENT));

        Event::assertNotDispatched(ExperimentNewVisitor::class);
    }

    public function test_is_experiment()
    {
        AbtestFacade::pageView();

        $this->assertTrue(AbtestFacade::isExperiment('firstExperiment'));
        $this->assertFalse(AbtestFacade::isExperiment('secondExperiment'));

        $this->assertEquals('firstExperiment', AbtestFacade::getExperiment()->name);
    }

    public function test_that_two_pageviews_do_not_count_as_two_visitors()
    {
        AbtestFacade::pageView();
        AbtestFacade::pageView();

        $experiment = session(Abtest::SESSION_KEY_EXPERIMENT);

        $this->assertEquals(1, $experiment->visitors);
    }

    public function test_that_isExperiment_triggers_pageview()
    {
        AbtestFacade::isExperiment('firstExperiment');

        $experiment = session(Abtest::SESSION_KEY_EXPERIMENT);

        $this->assertEquals($this->experiments[0], $experiment->name);
        $this->assertEquals(1, $experiment->visitors);
    }

    public function test_request_macro()
    {
        $this->newVisitor();

        $experiment = session(Abtest::SESSION_KEY_EXPERIMENT);

        $this->assertEquals($experiment, request()->abExperiment());
    }

    public function test_blade_macro()
    {
        $this->newVisitor();

        $this->assertTrue(Blade::check('ab', 'firstExperiment'));
    }

    public function test_that_isExperiment_works_with_crawlers()
    {
        config([
            'ab-testing.ignore_crawlers' => true,
        ]);
        $_SERVER['HTTP_USER_AGENT'] = 'Googlebot';

        $this->assertFalse(AbtestFacade::isExperiment('firstExperiment'));
    }
}
