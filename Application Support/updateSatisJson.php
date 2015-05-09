<?php
$blacklist = array(
    "QafooGalaxy/qafoogalaxy.github.io" => true,
    "QafooGalaxy/BootstrapRole" => true
);

$githubUrl = "https://api.github.com/orgs/QafooGalaxy/repos?page=";

$foundRepositories = array();
$page = 1;
do {
    $responseBody = file_get_contents(
        $githubUrl . $page,
        false,
        stream_context_create(
            array(
                "http" => array(
                    "method" => "GET",
                    "header" => array(
                        "Accept: application/vnd.github.v3+json",
                        "User-Agent: QafooGalaxy Satis Updater"
                    )
                )
            )
        )
    );

    echo "Fetching repository list.\n";

    if ($responseBody === false) {
        throw new \RuntimeException("Could not fetch repository list from github");
    }

    $repositories = json_decode($responseBody, true);

    if ($repositories === null) {
        throw new \RuntimeException("Retrieved JSON could not be decoded");
    }

    $foundRepositories = array_merge($foundRepositories, $repositories);
    $page++;

} while(count($repositories));

$foundRepositories = array_filter($foundRepositories, function($repository) use ($blacklist) {
    return array_key_exists($repository["full_name"], $blacklist) === false;
});

$satisRepositories = array_map(function($repository) {
    return array(
        "type" => "vcs",
        "url" => $repository["html_url"]
    );
}, $foundRepositories);

$satisConfiguration = array(
    "name" => "QafooAnsible",
    "homepage" => "http://qafoogalaxy.github.io",
    "repositories" => $satisRepositories,
    "require-all" => true,
    "twig-template" => "template/index.html.twig"
);

file_put_contents(
    "satis.json",
    json_encode($satisConfiguration, JSON_PRETTY_PRINT)
);

echo "satis.json updated.\n";
