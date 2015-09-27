Feature: Test add manifest

  @reset-schema
  Scenario: Fake background (for performance)
    Given I have users:
      | username | password | roles      |
      | test     | test     | ROLE_USER  |

  Scenario: As a simple user, upload layers and add a manifest
    Given I set basic authentication with "test" and "test"

    When I send a "HEAD" request to "/v2/test/hello-world/blobs/sha256:a3ed95caeb02ffe68cdd9fd84406680ae93d633cb16422d00e8a7c22955b46d4"
    Then the response status code should be 404

    And I send a "POST" request to "/v2/test/hello-world/blobs/uploads/"
    And the response status code should be 202
    And the header "Docker-Upload-UUID" should contain "-"
    And I store value of header "Docker-Upload-UUID" to variable "uuid"

    And I send a "PUT" request to "/v2/test/hello-world/blobs/uploads/{uuid}?digest=sha256%3Aa3ed95caeb02ffe68cdd9fd84406680ae93d633cb16422d00e8a7c22955b46d4" with body "layers/a3ed95caeb02ffe68cdd9fd84406680ae93d633cb16422d00e8a7c22955b46d4"
    And the response status code should be 201
    And the header "Location" should contain "/v2/test/hello-world/blobs/sha256:a3ed95caeb02ffe68cdd9fd84406680ae93d633cb16422d00e8a7c22955b46d4"
    And the header "Docker-Content-Digest" should be equal to "sha256:a3ed95caeb02ffe68cdd9fd84406680ae93d633cb16422d00e8a7c22955b46d4"

    And I send a "HEAD" request to "/v2/test/hello-world/blobs/sha256:a3ed95caeb02ffe68cdd9fd84406680ae93d633cb16422d00e8a7c22955b46d4"
    And the response status code should be 200

    And I send a "HEAD" request to "/v2/test/hello-world/blobs/sha256:03f4658f8b782e12230c1783426bd3bacce651ce582a4ffb6fbbfa2079428ecb"
    And the response status code should be 404

    And I send a "POST" request to "/v2/test/hello-world/blobs/uploads/"
    And the response status code should be 202
    And the header "Docker-Upload-UUID" should contain "-"
    And I store value of header "Docker-Upload-UUID" to variable "uuid"

    And I send a "PUT" request to "/v2/test/hello-world/blobs/uploads/{uuid}?digest=sha256%3A03f4658f8b782e12230c1783426bd3bacce651ce582a4ffb6fbbfa2079428ecb" with body "layers/03f4658f8b782e12230c1783426bd3bacce651ce582a4ffb6fbbfa2079428ecb"
    And the response status code should be 201
    And the header "Location" should contain "/v2/test/hello-world/blobs/sha256:03f4658f8b782e12230c1783426bd3bacce651ce582a4ffb6fbbfa2079428ecb"
    And the header "Docker-Content-Digest" should be equal to "sha256:03f4658f8b782e12230c1783426bd3bacce651ce582a4ffb6fbbfa2079428ecb"

    And I send a "PUT" request to "/v2/test/hello-world/manifests/latest" with body "manifests/test~hello-world:latest.json"
    And the response status code should be 201
    And the header "Location" should contain "/v2/test/hello-world/manifests/latest"
    And the header "Docker-Content-Digest" should be equal to "sha256:9956e7d769a4cfeba2e342b92adf58e403affd8a77ef0710c4fb01e948fc2bbe"
