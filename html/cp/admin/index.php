<?php

$this->respond('GET', '/', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        $service->render('index.phtml', array('action' => 'index', 'page' => 'cp/admin/index.phtml'));
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('GET', '/bot', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        $service->render('index.phtml', array('action' => 'bot', 'page' => 'cp/admin/bot/index.phtml'));
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('GET', '/user/approve', function($request, $response, $service, $app) {
    if (verifySession($app) && checkPermission($app, 'panel.viewusers')) {
        $perms['approveUser'] = checkPermission($app, 'panel.approveuser');
        $perms['deleteUser'] = checkPermission($app, 'panel.deleteuser');
        $service->render('index.phtml', array('action' => 'user', 'page' => 'cp/admin/user/approval.phtml', 'perms' => $perms));
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('GET', '/user/manage', function($request, $response, $service, $app) {
    if (verifySession($app) && checkPermission($app, 'panel.viewusers')) {
        $service->render('index.phtml', array('action' => 'user', 'page' => 'cp/admin/user/managegroups.phtml'));
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('GET', '/ban', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        $service->render('index.phtml', array('action' => 'ban', 'page' => 'cp/admin/ban/index.phtml'));
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('GET', '/user', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        if (checkPermission($app, 'panel.viewusers')) {
            $perms['approveUser'] = checkPermission($app, 'panel.approveuser');
            $perms['deleteUser'] = checkPermission($app, 'panel.deleteuser');
            $service->render('index.phtml', array('action' => 'user', 'page' => 'cp/admin/user/approval.phtml', 'perms' => $perms));
        }
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('POST', '/user/list/unapproved', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        $perms['view'] = checkPermission($app, 'panel.viewuser');
        if ($perms['view']) {
            try {
                $statement = $app->auth_db->prepare("SELECT uuid as id,username as user,email FROM users WHERE approved=0 and verified=1");
                $statement->execute();
                $accounts = $statement->fetchAll();
            } catch (PDOException $ex) {
                logError($ex);
                $accounts = array();
            }
        } else {
            $accounts = array();
        }
        echo json_encode($accounts);
    } else {
        echo "failed";
    }
});

$this->respond('POST', '/user/approve/[i:id]', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        try {
            $statement = $app->auth_db->prepare("UPDATE users SET approved=1 WHERE uuid=?");
            $statement->execute(array($request->id));
        } catch (PDOException $ex) {
            logError($ex);
        }
        $response->redirect("/user", 302);
    } else {
        $response->redirect("/auth/login", 302);
    }
});

$this->respond('POST', '/user/delete/[i:id]', function($request, $response, $service, $app) {
    if (verifySession($app)) {
        try {
            $statement = $app->auth_db->prepare("DELETE FROM users WHERE uuid=?");
            $statement->execute(array($request->id));
        } catch (PDOException $ex) {
            logError($ex);
        }
    } else {
        $response->redirect("/auth/login", 302);
    }
});