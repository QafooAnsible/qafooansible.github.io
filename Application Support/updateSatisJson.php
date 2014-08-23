<?php
$blacklist = array(
    "QafooAnsible/qafooansible.github.io" => true
);

$githubUrl = "https://api.github.com/orgs/QafooAnsible/repos";

$responseBody = file_get_contents(
    $githubUrl,
    false,
    stream_context_create(
        array(
            "http" => array(
                "method" => "GET",
                "header" => array(
                    "Accept: application/vnd.github.v3+json",
                    "User-Agent: QafooAnsible Satis Updater"
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

$repositories = array_filter($repositories, function($repository) use ($blacklist) {
    return array_key_exists($repository["full_name"], $blacklist) === false;
});

$satisRepositories = array_map(function($repository) {
    return array(
        "type" => "vcs",
        "url" => $repository["html_url"]
    );
}, $repositories);


$satisConfiguration = array(
    "name" => "QafooAnsible",
    "homepage" => "http://qafooansible.github.io",
    "repositories" => $satisRepositories,
    "require-all" => true,
    "twig-template" => "Template/index.html.twig"
);

file_put_contents(
    "satis.json",
    json_encode($satisConfiguration, JSON_PRETTY_PRINT)
);

echo "satis.json updated.\n";