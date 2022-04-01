# Ovh Cli

Small console application to update domains records and list some infos.

# Usage

```sh
# list al your domain
php domain:list --with-details --type domain --filter minicar 

# Get details for a service (e.g. a domain)
php service:details performing.digital

# Get zone details for a domain name (e.g. A record)
php zone:details performing.digital --type A

# Update the zone setting or creating a new record
php zone:update performing.digital --record "",A,0.0.0.0
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
