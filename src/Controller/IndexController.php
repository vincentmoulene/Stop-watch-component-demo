<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IndexController extends AbstractController
{
    public function index()
    {
        $stopwatch = new Stopwatch();
        $event = $stopwatch->start('test');
        sleep(1);
        $event->stop();

        $response = 'Duration : ' . $event->getDuration() . ' (milliseconds)' . '<br/>';
        $response .= 'Duration : ' . $event->getMemory() . ' (bytes)';

        return $this->render('base.html.twig', [
            'response' => $response,
        ]);
    }

    public function quotesDouble() {
        $bar = 'bar';
        $foo = 'foo';
        $stopwatch = new Stopwatch();
        $event = $stopwatch->start('test');
        sleep(1);
        $test = "";
        for ($i=1; $i<35;$i++) {
            $test .= "foo $bar baz $foo" . '<br/>';
        }
        $event->stop();

        $response = $test . '<br/>';
        $response .= 'Duration : ' . $event->getDuration() . ' (milliseconds)' . '<br/>';
        $response .= 'Duration : ' . $event->getMemory() . ' (bytes)';


        return $this->render('base.html.twig', [
            'response' => $response,
        ]);
    }

    public function quotesSimple() {
        $bar = 'bar';
        $foo = 'foo';
        $stopwatch = new Stopwatch();
        $event = $stopwatch->start('test');
        sleep(1);
        $test = '';
        for ($i=1; $i<35;$i++) {
            $test .= 'foo '.$bar.' baz '.$foo . '<br/>';
        }
        $event->stop();

        $response = $test . '<br/>';
        $response .= 'Duration : ' . $event->getDuration() . ' (milliseconds)' . '<br/>';
        $response .= 'Duration : ' . $event->getMemory() . ' (bytes)';


        return $this->render('base.html.twig', [
            'response' => $response,
        ]);
    }


    function fibonacci_test() {
        $stopwatch = new Stopwatch();
        for ($i=1; $i<35;$i++) {
            $eventName = sprintf('Fibonacci %d', $i);
            $event = $stopwatch->start($eventName, 'fibonacci');
            $this->fibonacci($i);
            $event->stop();

            printf("- %s: %dms\n", $eventName, $event->getDuration());
            echo '<br>';
        }
        die('Fin');
    }

    function fibonacci($n)
    {
        if ($n <= 2) {
            return 1;
        } else {
            return $this->fibonacci($n - 1) + $this->fibonacci($n - 2);
        }
    }

    function lap(){
        $stopwatch = new Stopwatch();

        $event = $stopwatch->start('files');

        // read file
        $contents = file_get_contents('data.yaml');
        $event->lap();

        // parse
        $parsed = Yaml::parse($contents);
        $event->lap();

        // generate output
        $output = '';
        foreach ($parsed as $item) {
            echo $item . '<br/>';
        }
        $event->lap();

        // write
        file_put_contents('output.txt', $output);
        $event->stop();

        var_dump($event->getDuration());
        echo '<br/>';
        foreach ($event->getPeriods() as $period) {
            var_dump($period->getDuration());
            echo '<br/>';
        }
        die('Fin');
    }

    public function sections(){
        $stopwatch = new Stopwatch();

        // section io (1)
        $stopwatch->openSection();
        $stopwatch->start('read_file', 'read');
        sleep(1);
        $stopwatch->stop('read_file');
        $stopwatch->stopSection('io');

        // section parsing (1)
        $stopwatch->openSection();
        $stopwatch->start('parse_file', 'yaml');
        sleep(1);
        $stopwatch->stop('parse_file');
        $stopwatch->stopSection('parsing');

        // section io (2)
        $stopwatch->openSection('io');
        $stopwatch->start('write_file', 'write');
        sleep(1);
        $stopwatch->stop('write_file');
        $stopwatch->stopSection('io');

        echo "Category 'io'\n";
        foreach ($stopwatch->getSectionEvents('io') as $event) {
            printf(" - %s: %d\n", $event->getCategory(), $event->getDuration());
            echo '<br/>';
        }

        echo "Category 'parsing'\n";
        foreach ($stopwatch->getSectionEvents('parsing') as $event) {
            printf(" - %s: %d\n", $event->getCategory(), $event->getDuration());
            echo '<br/>';
        }
        dump('Fin');
    }
}