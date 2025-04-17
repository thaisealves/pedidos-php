<?php
interface Calculavel
{ //interface a ser implementada pelas classes, garantindo a implementação
    public function calcular(): float;
}

interface Validavel
{
    public function validar(): void;
}

interface ItemInterface extends Calculavel, Validavel
{ //interface para os itens, garantindo que eles implementem o método calcularSubtotal
    public function calcularSubtotal(): float;

    //garantir sempre a existência desses métodos para a validação de preco e quantidade no pedido
    public function getPreco(): float;
    public function getQuantidade(): int;
}

class Validador
{   //classe de responsabilidade única para validação, substituindo a repetição de código
    //mensagens de erro mais específicas 
    //método estático por nao precisar de instância

    /**
     * @throws Exception Quando preço ou quantidade estão fora dos limites permitidos
     * @return void
     */
    public static function validarProduto(float $preco, int $quantidade): void
    {
        if ($preco < 0) {
            throw new Exception("Preço não pode ser negativo!");
        }

        if ($quantidade <= 0) {
            throw new Exception("Quantidade deve ser maior que zero!");
        }
    }
}

class Item implements ItemInterface
{
    //tornando os atributos privados por questões de encapsulamento
    //e segurança, evitando acesso direto
    private string $nome;
    private float $preco;
    private int $quantidade;


    public function __construct(string $nome, float $preco, int $quantidade)
    {
        $this->nome = $nome;
        $this->preco = $preco;
        $this->quantidade = $quantidade;
        $this->validar();
    }

    public function validar(): void
    {
        Validador::validarProduto($this->preco, $this->quantidade);
    }
    public function calcular(): float
    {
        return $this->calcularSubtotal();
    }

    //correção do calculo do subtotal, preço * quantidade
    public function calcularSubtotal(): float
    {
        return $this->preco * $this->quantidade;
    }

    // acesso aos atributos através de métodos getters
    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }
}


class Pedido implements Calculavel
{
    /** @var ItemInterface[] */
    private array $itens = [];


    //mantendo a dependency inverson principle, pedido dependendo da abstração e não da implementação
    public function adicionarItem(ItemInterface $item): void
    {
        //validação não precisa acontecer aqui porque ItemInterface obriga a implementação do método validar
        //então o item já está validado quando adicionado ao pedido
        $this->itens[] = $item;
    }

    public function calcular(): float
    {
        return $this->calcularTotal();
    }

    public function calcularTotal(): float
    {
        $total = 0;
        foreach ($this->itens as $item) {
            $total += $item->calcularSubtotal();
        }
        return $total;
    }

    /**
     * @return ItemInterface[] 
     */
    public function getItens(): array
    {
        return $this->itens;
    }
}
