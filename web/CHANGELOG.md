# gsfReim



## Bug Fixes
  - Hardcode ESI versions so as to not break when CCP randomly changes the latest
  ([2629e3f7](git@github.com:kilgarth/gsfReim/commit/2629e3f71f3e1adceb083117062a0cf3201c6686))
  - Sessions were not set correctly, use redis.
  ([3525e5d4](git@github.com:kilgarth/gsfReim/commit/3525e5d4bde01aa29d34d756677454780e62a49a))
  - Adjust the docroot in .platform.app.yaml
  ([c25aa817](git@github.com:kilgarth/gsfReim/commit/c25aa8178c8f9a49fd35541a1c4f671935f6f621))
  - Added additional check to make sure we get valid data back from api
  ([7a625947](git@github.com:kilgarth/gsfReim/commit/7a625947a833f96c5b49989e05c9352f4cfdbe02))




## Refactor
  - Refactor the way configurations are stored such that everything is in config.php and database.php
  ([223b167f](git@github.com:kilgarth/gsfReim/commit/223b167f741a101b33971bef9040ffb31e775f38))
  - Move docroot to subdir and update the configs so that everything variable is in one location
  ([bff7764d](git@github.com:kilgarth/gsfReim/commit/bff7764dbe3bd88e16b4d99eead78c667a692220))





---
<sub><sup>*Generated with [git-changelog](https://github.com/rafinskipg/git-changelog). If you have any problems or suggestions, create an issue.* :) **Thanks** </sub></sup>
