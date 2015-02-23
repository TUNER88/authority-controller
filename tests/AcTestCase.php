<?php

use Mockery as m;

abstract class AcTestCase extends Orchestra\Testbench\TestCase
{
    protected $app;
    protected $router;

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    protected function mock($className)
    {
        $mock = m::mock($className);
        App::bind($className, function ($app, $parameters = []) use ($mock) {
            if (is_array($parameters) && is_array($attributes = array_get($parameters, 0, [])) && respond_to($mock, "fill")) {
                $mock = $this->fillMock($mock, $attributes);
            }

            return $mock;
        });

        return $mock;
    }

    protected function fillMock($mock, $attributes = [])
    {
        $instance = $mock->makePartial();
        foreach ($attributes as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }

    protected function getPackageProviders()
    {
        return [
            'Collective\Html\HtmlServiceProvider',
            'Efficiently\AuthorityController\AuthorityControllerServiceProvider',
        ];
    }

    protected function getPackageAliases()
    {
        return [
            'Form' => 'Collective\Html\FormFacade',
            'Html' => 'Collective\Html\HtmlFacade',
            'Authority' => 'Efficiently\AuthorityController\Facades\Authority',
            'Params'    => 'Efficiently\AuthorityController\Facades\Params',
        ];
    }
}
