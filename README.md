# Composer Grunt bridge

*Grunt integration for Composer packages.*

A quick proof of concept based entirely on (and shamelessly stolen from) [the composer-npm-bridge](https://github.com/eloquent/composer-npm-bridge).

Basically, add this to your `composer.json` requirements to have composer run the default grunt task for your project on install or update. You can specify a certain non-default task or list of tasks using the `"extra"` object:

```json
// Specify one task to run
{
  "extra": {
    "grunt-task": "build"
  }
}
// OR specify multiple tasks to run
{
  "extra": {
    "grunt-task": ["sass", "concat", "uglify", "cssmin"]
  }
}
```

