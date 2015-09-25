iamluc/dunkerque
================

# About

Docker hub & registry image

See: [https://github.com/iamluc/dunkerque](https://github.com/iamluc/dunkerque)

# Usage

Run a simple container:

```sh
docker run --name dunkerque --rm -p 9999:80 iamluc/dunkerque
```

Or using a data container:

```
docker create --name dunkerque_data iamluc/dunkerque
docker run --name dunkerque --volumes-from dunkerque_data --rm -p 9999:80 iamluc/dunkerque
```

# Storage

By default, storage (database + layers) is done in `/data`
