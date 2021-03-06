<?php

namespace Tests;

use L5Swagger\Exceptions\L5SwaggerException;

class RoutesTest extends TestCase
{
    /** @test */
    public function userCantAccessJsonFileIfItIsNotGenerated(): void
    {
        $jsonUrl = route('l5-swagger.docs');

        $response = $this->get($jsonUrl);
        $this->assertTrue($response->isNotFound());
    }

    /** @test */
    public function userCanAccessJsonFileIfItIsGenerated(): void
    {
        $jsonUrl = route('l5-swagger.docs');

        $this->crateJsonDocumentationFile();

        $this->get($jsonUrl)
            ->assertSee('{}')
            ->isOk();
    }

    /** @test */
    public function userCanAccessAndGenerateCustomJsonFile(): void
    {
        $customJsonFileName = 'docs.v1.json';

        $jsonUrl = route('l5-swagger.docs', $customJsonFileName);

        $this->setCustomDocsFileName($customJsonFileName);
        $this->crateJsonDocumentationFile();

        $this->get($jsonUrl)
            ->assertSee('{}')
            ->isOk();
    }

    /** @test */
    public function userCanAccessDocumentationInterface(): void
    {
        $this->get(config('l5-swagger.routes.api'))
            ->assertSee(route('l5-swagger.docs', config('l5-swagger.paths.docs_json', 'api-docs.json')))
            ->assertSee(route('l5-swagger.oauth2_callback'))
            ->isOk();
    }

    /** @test */
    public function itCanServeAssets(): void
    {
        $this->get(l5_swagger_asset('swagger-ui.css'))
            ->assertSee('.swagger-ui')
            ->isOk();
    }

    /** @test */
    public function itWillThrowExceptionForIncorrectAsset(): void
    {
        $this->expectException(L5SwaggerException::class);
        $this->expectExceptionMessage('(bad-swagger-ui.css) - this L5 Swagger asset is not allowed');

        l5_swagger_asset('bad-swagger-ui.css');
    }

    /** @test */
    public function itHandleBadAssetRequest(): void
    {
        $this->get(route('l5-swagger.asset', 'file.css'))
            ->assertNotFound();
    }

    /** @test */
    public function userCanAccessOauth2Redirect(): void
    {
        $this->get(route('l5-swagger.oauth2_callback'))
            ->assertSee('swaggerUIRedirectOauth2')
            ->assertSee('oauth2.auth.code')
            ->isOk();
    }
}
