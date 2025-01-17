<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Laminas\Code\Generator\ClassGenerator;
use Phpro\SoapClient\CodeGenerator\ClientFactoryGenerator;
use Phpro\SoapClient\CodeGenerator\Context\ClassMapContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ClientFactoryContext;
use PHPUnit\Framework\TestCase;
use Laminas\Code\Generator\FileGenerator;

class ClientFactoryGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $expected = <<<BODY
<?php

namespace App\Client;

use App\Client\Myclient;
use App\Classmap\SomeClassmap;
use Symfony\Component\EventDispatcher\EventDispatcher;
<<<<<<< HEAD
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Phpro\SoapClient\Event\Subscriber\LogSubscriber;
use Phpro\SoapClient\Caller\EventDispatchingCaller;
use Phpro\SoapClient\Caller\EngineCaller;
use Psr\Log\LoggerInterface;

class MyclientFactory
{
    public static function factory(string \$wsdl, \Psr\Log\LoggerInterface \$logger = null) : \App\Client\Myclient
    {
        \$engine = DefaultEngineFactory::create(
            ExtSoapOptions::defaults(\$wsdl, [])
                ->withClassMap(SomeClassmap::getCollection())
        );

        \$eventDispatcher = new EventDispatcher();
        \$caller = new EventDispatchingCaller(new EngineCaller(\$engine), \$eventDispatcher);

        if(\$logger) {
            \$eventDispatcher->addSubscriber(new LogSubscriber(\$logger));
        }
        
        return new Myclient(\$caller);
    }
}


BODY;
        $clientContext = new ClientContext(new ClassGenerator(), 'Myclient', 'App\\Client');
        $classMapContext = new ClassMapContext(
            new FileGenerator(),
            new \Phpro\SoapClient\CodeGenerator\Model\TypeMap('App\\Types', []),
            'SomeClassmap',
            'App\\Classmap'
        );
        $context = new ClientFactoryContext($clientContext, $classMapContext);
        $generator = new ClientFactoryGenerator();
        self::assertEquals($expected, $generator->generate(new FileGenerator(), $context));
    }
}
