# Almavia Cx Integration

## Create a new data.json file and template page, run drush command

````bash
ddev drush almavia:add-page
````

After executing the command, please enter a page title.

```bash
Please enter a page title (Ex : Contact):
> Accueil
```

Then validate your request.

```bash
 2/2 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 [OK] Both files have been created. The caches have been emptied.

 ------ --------------------------------------------------------------------------------------
  Type   File
 ------ --------------------------------------------------------------------------------------
  json   modules/custom/almavia_integration/data/accueil.json
  twig   modules/custom/almavia_integration/templates/almavia--integration--accueil.html.twig
 ------ --------------------------------------------------------------------------------------
```

## Medias

To reference media in the asset folder, use the {{ module_path }} variable in the branch files
- Output `{{ module_path }}` variable :  `modules/custom/almavia_integration`

```twig
<img src="/{{ module_path }}/assets/<media-name.png>" alt="" width="306" height="306">
```

