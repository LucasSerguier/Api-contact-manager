<?php
// Routes
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$db = new PDO('sqlite:db.sqlite3');

$app->get('/install', function () use ($db){
  try {
    $db->exec('  CREATE TABLE IF NOT EXISTS groupes (
                    id INTEGER PRIMARY KEY,
                    nom TEXT
                    );');

      $db->exec('  CREATE TABLE IF NOT EXISTS contacts (
                      id INTEGER PRIMARY KEY,
                      nom TEXT,
                      prenom TEXT,
                      numero INTEGER,
                      mail TEXT,
                      groupe INTEGER,
                      FOREIGN KEY(groupe) REFERENCES groupes(id)
                      );');
      echo 'Tables groupes et contacts crées';
  } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
  }


});

$app->get('/contact', function () use ($db) {
    $sql = "SELECT * FROM contacts;";
    $req = $db->query($sql);
    echo json_encode($req->fetchAll(PDO::FETCH_OBJ));
});

$app->get('/groupe', function () use ($db) {
    $sql = "SELECT * FROM groupes;";
    $req = $db->query($sql);
    echo json_encode($req->fetchAll(PDO::FETCH_OBJ));
});

$app->get('/contact/{id}', function (Request $request, Response $response, $args) use ($db) {
    $id = $args['id'];
    $sql = "SELECT * FROM contacts WHERE id = :id LIMIT 1;";
    try {
      $req = $db->prepare($sql);
      $req->bindParam("id", $id);
      $req->execute();
      echo json_encode($req->fetchObject());
  } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
});

$app->get('/groupe/{id}', function (Request $request, Response $response, $args) use ($db) {
    $id = $args['id'];
    $sql = "SELECT * FROM groupes WHERE id = :id LIMIT 1;";
    try {
      $req = $db->prepare($sql);
      $req->bindParam("id", $id);
      $req->execute();
      echo json_encode($req->fetchObject());
  } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
});

$app->post('/contact', function (Request $request, Response $response) use ($db) {
  $contact = $request->getParsedBody();
  $sql = "INSERT INTO contacts (nom, prenom, numero, mail, groupe) VALUES (:nom, :prenom, :numero, :mail, :groupe)";
  try {
      $req = $db->prepare($sql);
      $req->bindParam("nom", $contact['nom']);
      $req->bindParam("prenom", $contact['prenom']);
      $req->bindParam("numero", $contact['numero']);
      $req->bindParam("mail", $contact['mail']);
      $req->bindParam("groupe", $contact['groupe']);

      $req->execute();
      $contact['id'] = $db->lastInsertId();
      $db = null;
      echo json_encode($contact);
  } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
});

$app->post('/groupe', function (Request $request, Response $response, $args) use ($db) {
  $groupe = $request->getParsedBody();
  $sql = "INSERT INTO groupes (nom) VALUES (:nom)";
  try {
      $req = $db->prepare($sql);
      $req->bindParam("nom", $groupe['nom']);

      $req->execute();
      $groupe['id'] = $db->lastInsertId();
      $db = null;
      echo json_encode($groupe);
  } catch(PDOException $e) {
      echo '{"error":{"text":'. $e->getMessage() .'}}';
  }
});

$app->put('/contact/{id}', function (Request $request, Response $response, $args) use ($db) {
    $id = $args['id'];
    $contact = $request->getParsedBody();

    $sql = "UPDATE contacts SET nom = :nom, prenom = :prenom, numero = :numero, mail = :mail, groupe = :groupe WHERE id = :id;";
    try {
        $req = $db->prepare($sql);
        $req->bindParam("nom", $contact['nom']);
        $req->bindParam("prenom", $contact['prenom']);
        $req->bindParam("numero", $contact['numero']);
        $req->bindParam("mail", $contact['mail']);
        $req->bindParam("groupe", $contact['groupe']);
        $req->bindParam("id", $id);

        $req->execute();
        $db = null;
        echo json_encode($contact);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

});

$app->put('/groupe/{id}', function (Request $request, Response $response, $args) use ($db) {
    $id = $args['id'];
    $groupe = $request->getParsedBody();

    $sql = "UPDATE groupes SET nom = :nom WHERE id = :id;";
    try {
        $req = $db->prepare($sql);
        $req->bindParam("nom", $groupe['nom']);
        $req->bindParam("id", $id);

        $req->execute();
        $db = null;
        echo json_encode($groupe);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

});

$app->delete('/contact/{id}', function (Request $request, Response $response, $args) use ($db) {
    $id = $args['id'];
    $sql = "DELETE FROM contacts WHERE id = :id;";
    try {
      $req = $db->prepare($sql);
      $req->bindParam("id", $id);
      $req->execute();
      $db = null;
      echo 'utilisateur '.$id.' a été supprimé';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});

$app->delete('/groupe/{id}', function (Request $request, Response $response, $args) use ($db) {
    $id = $args['id'];
    $sql = "DELETE FROM groupes WHERE id = :id;";
    try {
      $req = $db->prepare($sql);
      $req->bindParam("id", $id);
      $req->execute();
      $db = null;
      echo 'groupe '.$id.' a été supprimé';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});
