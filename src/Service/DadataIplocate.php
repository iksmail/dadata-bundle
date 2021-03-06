<?php

declare(strict_types=1);

namespace Velhron\DadataBundle\Service;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Velhron\DadataBundle\Exception\DadataException;
use Velhron\DadataBundle\Model\Request\AbstractRequest;
use Velhron\DadataBundle\Model\Request\Iplocate\IplocateRequest;
use Velhron\DadataBundle\Model\Response\Suggest\AddressResponse;

class DadataIplocate extends AbstractService
{
    /**
     * Обработчик для API по IP-адресу.
     *
     * @throws DadataException
     */
    private function handle(string $method, string $ip, array $options = [])
    {
        $requestClass = $this->resolver->getMatchedRequest($method);
        $responseClass = $this->resolver->getMatchedResponse($method);

        /* @var IplocateRequest $request */
        $request = new $requestClass();
        $request
            ->setQuery($ip)
            ->fillOptions($options);

        $responseData = $this->query($request);

        return isset($responseData['location']) ? new $responseClass($responseData['location']) : null;
    }

    /**
     * {@inheritdoc}
     */
    protected function query(AbstractRequest $request): array
    {
        try {
            $response = $this->httpClient->request('GET', $request->getUrl(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Token {$this->token}",
                ],
                'query' => $request->getBody(),
            ]);

            return json_decode($response->getContent(), true) ?? [];
        } catch (ExceptionInterface $exception) {
            throw new DadataException($exception);
        }
    }

    /**
     * Город по IP-адресу.
     *
     * - Определяет город по IP-адресу в России
     * - Поддерживает как IPv4, так и IPv6 адреса
     * - Возвращает детальную информацию о городе, в том числе почтовый индекс
     *
     * @param string $ip      - ip-адрес
     * @param array  $options - дополнительные параметры запроса
     *
     * @return AddressResponse|null - ответ
     *
     * @throws DadataException
     */
    public function iplocateAddress(string $ip, array $options = []): ?AddressResponse
    {
        return $this->handle('iplocateAddress', $ip, $options);
    }
}
