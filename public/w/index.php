<?php


// _____________________________________________________ R E Q U I R E ________________________________________________________________

require('../../vendor/autoload.php');
require('../../fn/fn.php');

function my_autoloader($class)
{
    require('../../class/class.w.' . strtolower($class) . '.php');
}
spl_autoload_register('my_autoloader');


// ________________________________________________________ I N S T A L _________________________________________________

$app = new App();
$aff = new Aff();


$config = $app->readconfig();
if (!$config) {
    $message = 'config_file_error';
    echo $message;
    if (isset($_POST['config']) && $_POST['config'] == 'create') {
        $config = $app->createconfig($_POST);
        $app->savejson($config->tojson());
        header('Location: ./');

    } else {
        $aff->configform();
    }
    exit;
}


// _________________________________________________________ S E S ___________________________________________________________

session();
if (!isset($_SESSION['level'])) {
    $session = 0;
} else {
    $session = $_SESSION['level'];
}

$app->setsession($session);


// __________________________________________________________ I D _______________________________________________

if (isset($_GET['id'])) {
    $app->setbdd($config);
}


// _______________________________________________________ A C T I O N __________________________________________________________________


if (isset($_POST['action'])) {
    switch ($_POST['action']) {

        case 'login':
            $_SESSION['level'] = $app->login($_POST['pass'], $config);
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ./');
            }
            break;

        case 'logout':
            $_SESSION['level'] = $app->logout();
            if (isset($_GET['id'])) {
                header('Location: ?id=' . $_GET['id']);
            } else {
                header('Location: ./');
            }
            break;

        case 'addmedia':
            $message = $app->addmedia($_FILES, 2 ** 24, $_POST['id']);
            header('Location: ./?aff=media&message=' . $message);
            break;

        case 'addcss':
            $message = $app->addcss($_FILES, 2 ** 24, $_POST['id']);
            header('Location: ./?aff=admin&message=' . $message);
            break;

        case 'editconfig':
            $config->hydrate($_POST);
            $app->savejson($config->tojson());
            header('Location: ./?aff=admin');

            break;


    }
}


// _____________________________________________________ D A T A B A S E __________________________________________________________________

if (isset($_POST['action'])) {
    $app->setbdd($config);

    switch ($_POST['action']) {

        case 'update':
            if ($app->exist($_GET['id'])) {
                $art = new Art($_POST);
                $app->update($art);
                header('Location: ?id=' . $art->id() . '&edit=1');
            }
            break;

        case 'copy':
            if ($app->exist($_GET['id'])) {
                $copy = $app->get($_POST['copy']);
                $art = $app->get($_POST['id']);
                if (!empty($_POST['css'])) {
                    $art->setcss($copy->css());
                }
                if (!empty($_POST['color'])) {
                    $art->setcouleurtext($copy->couleurtext());
                    $art->setcouleurbkg($copy->couleurbkg());
                    $art->setcouleurlien($copy->couleurlien());
                    $art->setcouleurlienblank($copy->couleurlienblank());
                }
                if (!empty($_POST['html'])) {
                    $art->sethtml($copy->md());
                }
                if (!empty($_POST['template'])) {
                    $art->settemplate($copy->template());
                }
                $app->update($art);
                header('Location: ?id=' . $art->id() . '&edit=1');
            }
            break;

        case 'delete':
            if ($app->exist($_GET['id'])) {
                $art = new Art($_POST);
                $app->delete($art);
                header('Location: ?id=' . $art->id());
            }
            break;

    }

}

if (isset($_POST['actiondb'])) {
    $app->setbdd($config);

    switch ($_POST['actiondb']) {

        case 'addtable':
            if (isset($_POST['tablename'])) {
                $message = $app->addtable($config->dbname(), $_POST['tablename']);
                header('Location: ./?aff=admin&message=' . $message);
            }
            break;

    }
}
// _______________________________________________________ H E A D _____________________________________________________________

if (isset($_GET['id'])) {
    $app->setbdd($config);
    if ($app->exist($_GET['id'])) {
        $art = $app->get($_GET['id']);
        if (!isset($_GET['edit'])) {
            $_GET['edit'] = 0;
        }
        $aff->arthead($art, $config->cssread(), $_GET['edit']);
    } else {
        $aff->head($_GET['id'], '');

    }
} elseif (isset($_GET['aff'])) {
    $aff->head($_GET['aff'], $_GET['aff']);
} else {
    $aff->head('home', 'home');
}







// _____________________________________________________ A L E R T _______________________________________________________________ 

if (isset($_GET['message'])) {
    echo '<span class="alert">' . $_GET['message'] . '</span>';
}








// ______________________________________________________ B O D Y _______________________________________________________________ 

echo '<body>';
$aff->nav($app);

if (isset($_GET['id'])) {
    $app->setbdd($config);


    if ($app->exist($_GET['id'])) {

        $art = $app->get($_GET['id']);

        if (isset($_GET['edit']) and $_GET['edit'] == 1 and $app->session() >= $app::EDITOR) {
            echo '<section class=edit>';
            $aff->edit($art, $app, $app->getlister(['id', 'titre']));
            $aff->copy($art, $app->getlister(['id', 'titre']));
            $aff->aside($app);
            echo '</section>';
        } else {
            echo '<section class="lecture">';
            $aff->lecture($art, $app);
            echo '</section>';

        }
    } else {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'new') {
                $art = new Art($_GET);
                $art->reset();
                $app->add($art);
                header('Location: ?id=' . $_GET['id'] . '&edit=1');
            }
        } else {
            echo '<span class="alert">This article does not exist yet</span>';

            if ($app->session() >= $app::EDITOR) {
                echo '<form action="?id=' . $_GET['id'] . '&edit=1" method="post"><input type="hidden" name="action" value="new"><input type="submit" value="Create"></form>';
            }

        }

    }
} elseif (isset($_GET['tag'])) {
    $app->setbdd($config);
    echo '<h4>' . $_GET['tag'] . '</h4>';
    $aff->tag($app->getlister(['id', 'titre', 'intro', 'tag'], 'id'), $_GET['tag'], $app);

} elseif (isset($_GET['lien'])) {
    $app->setbdd($config);
    echo '<h4><a href="?id=' . $_GET['lien'] . '">' . $_GET['lien'] . '</a></h4>';
    $aff->lien($app->getlister(['id', 'titre', 'intro', 'lien']), $_GET['lien'], $app);

} elseif (isset($_GET['aff']) && $app->session() >= $app::EDITOR) {
    if ($_GET['aff'] == 'admin' && $app->session() >= $app::ADMIN) {
        echo '<section>';
        echo '<h1>Admin</h1>';
        
        
        
        //       $app->tableexist($config->dbname(), 'guigui');

        $aff->admincss($config, $app);
        $aff->adminpassword($config);
        $aff->admindb($config);
        if ($app->setbdd($config)) {
            //var_dump($app->tablelist($config->dbname()));
            echo '<p>database status : OK</p>';
        }
        $aff->admintable($config, $app->tablelist($config->dbname()));

        echo '</section>';
    } elseif ($_GET['aff'] == 'media') {
        echo '<h1>Media</h1>';
        echo '<section>';

        $aff->addmedia($app);
        $aff->medialist($app);

        echo '</section>';

    } elseif ($_GET['aff'] == 'record') {
        echo '<h1>Record</h1>';
        echo '<section>';

        $aff->recordlist($app);

        echo '</section>';

    } elseif ($_GET['aff'] == 'map') {
        $app->setbdd($config);
        $aff->map($app, $config->domain());
    } else {
        header('Location: ./');
    }

} else {

    $aff->header();

    echo '<section class="home">';


    $app->setbdd($config);
    $opt = new Opt(Art::classvarlist());
    $opt->hydrate($_GET);
    $opt->setcol(['id', 'tag', 'lien', 'contenu', 'intro', 'titre', 'datemodif', 'datecreation', 'secure']);
    $table = $app->getlisteropt($opt);
    $app->listcalclien($table);
    $opt->settaglist($table);
    $opt->setcol(['id', 'tag', 'lien', 'contenu', 'intro', 'titre', 'datemodif', 'datecreation', 'secure', 'liento']);

    $aff->option($app, $opt);

    $filtertagor = $app->filtertagor($table, $opt->tagor());
    $filtersecure = $app->filtersecure($table, $opt->secure());

    $filter = array_intersect($filtertagor, $filtersecure);
    $table2 = [];
    foreach ($table as $art) {
        if (in_array($art->id(), $filter)) {
            $table2[] = $art;
        }
    }

    $app->artlistsort($table2, $opt->sortby(), $opt->order());



    $aff->home2table($app, $table2);

    echo '</section>';

}

echo '</body>';


?>
