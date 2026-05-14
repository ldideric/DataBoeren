{ pkgs, lib, config, inputs, ... }:

{
  dotenv.disableHint = true;

  languages.php = {
    enable = true;
    version = "8.4";
  };

  languages.javascript = {
    enable = true;
    package = pkgs.nodejs_24;
  };

  processes = {
    serve.exec = "php artisan serve";
    vite.exec = "npm run dev";
    queue.exec = "php artisan queue:work";
  };
}
