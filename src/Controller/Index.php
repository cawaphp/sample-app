<?php
declare (strict_types=1);

namespace SampleApp\Controller;

use Cawa\Cache\Cache;
use Cawa\Core\App;
use Cawa\Core\Controller\AbstractController;
use Cawa\Core\Controller\Renderer\HtmlContainer;
use Cawa\Core\Controller\Renderer\HtmlElement;
use Cawa\Core\Controller\Renderer\HtmlPage;
use Cawa\Core\Controller\Renderer\Phtml;
use Cawa\Core\Controller\Renderer\Twig;
use Cawa\Date\DateTime;
use Cawa\Email\Message;
use Cawa\Http\Client;
use Cawa\Orm\CollectionPaged;
use Cawa\Orm\Model;
use Cawa\Orm\Session;

/**
 */
class Index extends AbstractController
{
    use Phtml, Twig {
        Phtml::setTemplatePath insteadof Twig;
        Phtml::getData insteadof Twig;
        Phtml::render insteadof Twig;
        Twig::render as private twigRender;
    }

    /**
     * @var array
     */
    private $data = array();

    /**
     * @return void
     */
    public function redirect()
    {
        App::response()->redirect('/' . App::translator()->getLocale());
    }

    /**
     * @param null $name
     *
     * @return string
     */
    public function method($name = null)
    {
        /*
        if (!$ip = Ip::sessionReload("IP")) {
            $ip = Ip::getById(12);
            $ip->sessionSave("IP");
        }
        */

        $list = Ip::getAll();
        $find = $list->find('getCountry', 'JP');

        $cache = Cache::create(["type" => "Apc", "prefix"=> "bla", "config" => "redis://localhost:6379"]);
        $cache->set("keys1.1", "value 1.1", null, ["key1"]);
        $cache->set("keys1.2", "value 1.2", null, ["key1"]);
        $cache->set("keys2.1", "value 2.1", null, ["key2"]);
        trace($cache->deleteKeysByTag("key1"));


        $message = new Message();
        $message
            ->setSubject('Your subject')
            ->setFrom(array('john@doe.com' => 'John Doe'))
            ->setTo(array('john@doe.com'))
            ->setHtmlBody("sdfsdf");
        // $message->send();


        $http = new Client();
        $http->get("http://www.google.com");

        // $this->app->response->redirect("/");
        $container = new HtmlContainer('<div>');

        $html = new HtmlElement('<a>');
        $html->setContent('Click me')->addClass(array('btn', 'btn-primary'));
        $container->add($html);

        $html = new HtmlElement('<a>');
        $html->setContent('Click now')->addClass(array('btn', 'btn-default'));
        $container->add($html);

        $this->data['main'] = $container->render();
        $this->data['plural'] =
            DateTime::now()->diffForHumans(DateTime::parse('-1 day')) . '<br />' .
            DateTime::now()->formatLocalized('%A %d %B %Y')  . '<br />' .
            App::translator()->transChoice('transChoice', 10, [10]) . '<br />' .
            App::translator()->transChoice('transChoice', 1, [1])
        ;

        $page = new MasterPage();
        $page->setMain($this->render())
            ->setAside('ASIDE FROM ' . get_class() . ' with name ' . $name)
            ->addCss('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css')
            //->addCss("test.css")
            //->addJs("test.js")
        ;

        return $page->render();
    }

    /**
     * @param null $name
     *
     * @return string
     */
    public function twig($name = null)
    {
        $this->data['navigation'][] = [
            'href' => '/',
            'caption' => 'Home',
        ];

        $this->data['test'] = 'dynamic vars';

        return $this->twigRender();
    }

    /**
     * @return string
     */
    public function notFound() : string
    {
        $page = new HtmlPage();
        $page->getBody()->setContent('<h1>404 Not Found</h1>');

        return $page->render();
    }
}

class Test extends Model
{

    /**
     * @var string
     */
    private $test = 'bla';

    /**
     * {@inheritdoc}
     */
    protected function map(array $result)
    {
        $this->test = null;
    }
}

class Ip extends Test
{
    use Session;

    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @var string
     */
    private $country;

    /**
     * @return string
     */
    public function getCountry() : string
    {
        return $this->country;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public static function getById(int $id)
    {
        $db = App::di()->getDb('MAIN');
        $sql = 'select * from ip_to_country LIMIT 1';
        if ($result = $db->fetchOne($sql, array('id' => $id))) {
            $return = new static();
            $return->map($result);

            return $return;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function map(array $result)
    {
        $this->from = (int) $result['ip_to_country_ip_from'];
        $this->to = (int) $result['ip_to_country_ip_to'];
        $this->country = $result['ip_to_country_country_code'];

        parent::map($result);
    }

    /**
     * @return CollectionPaged
     */
    public static function getAll()
    {
        $return = [];
        $db = App::di()->getDb('MAIN');
        $sql = 'select SQL_CALC_FOUND_ROWS * from ip_to_country LIMIT 10';
        foreach ($db->query($sql) as $result) {
            $item = new static();
            $item->map($result);
            $return[] = $item;
        }

        $count = $db->fetchOne('SELECT FOUND_ROWS() as count')['count'];

        $collection = new CollectionPaged($return, (int) $count, 1);

        return $collection;
    }
}
