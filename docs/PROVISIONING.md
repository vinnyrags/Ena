# Provisioning conventions (Mythus/IX droplet sites)

The canonical infra conventions a new Mythus/IX site inherits when provisioned onto
a DigitalOcean droplet. Ena is a *theme* starter, so it carries the **conventions +
templates**, not a full droplet-provisioning script — each site keeps its own
(gitignored) `scripts/provision-*.sh`. Keep those scripts in line with what's here.

## Page caching — 30s FastCGI microcache, no purge

The platform standard is a **short-TTL (30s) nginx FastCGI microcache** with a
logged-in/POST bypass — **not** long-TTL + purge-on-save, and **not** nginx-helper.
Full config in [`scripts/nginx-cache.conf.template`](../scripts/nginx-cache.conf.template).

Two rules that were learned the hard way (both were live bugs on MF/AVFTB, since fixed):

- **`fastcgi_cache_valid 200 30s;` — cache 200s ONLY, never 301/302.** A cached
  redirect (a transient http→https or trailing-slash 301 captured during setup)
  pins a redirect loop for the whole TTL → `ERR_TOO_MANY_REDIRECTS` for every
  visitor. A provisioning default of `200 301 302 30m` is the trap — don't ship it.
- **30s, not 30m.** It's a microcache: editors don't chase a stale page, and a short
  TTL converges on the same origin protection as long-TTL+purge exactly when traffic
  warrants it — without any purge machinery, plugin, or Redis.

There is **no origin purge driver** — the TTL is the correctness floor. (Mythus's old
`PurgePageCache` hook + the cache-invalidation seam were removed as YAGNI; see the
akivili `platform-consolidation-plan.md`.)

## Object cache (Redis) — off by default, per-site opt-in

Do **not** install/enable `redis-server` or define `WP_REDIS_*` by default. A running
Redis with no `object-cache.php` drop-in is just idle RAM + dead config. Object
caching is a deliberate per-site opt-in (currently only vincentragosta.io). If a site
genuinely needs it, add `redis-server` + the drop-in + `WP_REDIS_*` for *that* site.

(The `php-redis` PHP extension is harmless if present, but don't install it as a
default either — nothing uses it without the drop-in.)

## Provisioning-script checklist

When writing/refreshing a site's `scripts/provision-*.sh`, verify:

- [ ] nginx cache block matches `nginx-cache.conf.template` (`200 30s`, no `301/302`, logged-in/POST/admin bypass maps)
- [ ] `apt-get install` does **not** include `redis-server` (nor `php*-redis` unless the site opts into object caching)
- [ ] `systemctl enable --now` does **not** include `redis-server`
- [ ] the `wp-config-env.php` heredoc does **not** define `WP_REDIS_*` (nor `RT_WP_NGINX_HELPER_*` — no nginx-helper)
- [ ] staging vhost is `noindex` (nginx `X-Robots-Tag` + `blog_public=0`)
