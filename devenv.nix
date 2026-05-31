{ pkgs, lib, config, inputs, ... }:
{
  dotenv.disableHint = true;
  processes = {
    serve.exec = "php artisan serve";
    vite.exec = "npm run dev";
    queue.exec = "php artisan queue:work";
  };
}
