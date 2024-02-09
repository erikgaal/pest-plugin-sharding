This repository contains the Pest Plugin Sharding.

# Usage

Use this plugin to split your test runs into multiple "shards".  This is a useful addition to `parallel`
on environments with limited vertical scaling and high horizontal scaling, like GitHub Actions.

```sh
pest --shard 1/4

pest --shard-index 1 --shard-total 4
```

Pest is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.
