<?php

class ProductEntity
{
    private int $id;
    private string $name;
    private string $product_type;
    private bool $for_women;
    private string $description;
    private int $price;
    private string $imageUrl; // Добавлено поле

    /**
     * @param int $id
     * @param string $name
     * @param string $product_type
     * @param bool $for_women
     * @param string $description
     * @param int $price
     * @param string $imageUrl // Добавлен параметр
     */
    public function __construct(int $id, string $name, string $product_type, bool $for_women, string $description, int $price, string $imageUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->product_type = $product_type;
        $this->for_women = $for_women;
        $this->description = $description;
        $this->price = $price;
        $this->imageUrl = $imageUrl; // Присваивание поля
    }

    /**
     * @throws NotFound
     */
    public static function objectToEntity($object): ProductEntity
    {
        if (!isset($object['id'])) {
            throw new NotFound("ID is not exist");
        }
        self::checkPropertyOrSetDefault($object, 'id');
        self::checkPropertyOrSetDefault($object, 'name');
        self::checkPropertyOrSetDefault($object, 'product_type');
        self::checkPropertyOrSetDefault($object, 'for_women', false);
        self::checkPropertyOrSetDefault($object, 'description');
        self::checkPropertyOrSetDefault($object, 'price', 0);
        self::checkPropertyOrSetDefault($object, 'imageUrl'); // Убедитесь, что поле imageUrl существует
        return new ProductEntity(
            $object['id'],
            $object['name'],
            $object['product_type'],
            $object['for_women'],
            $object['description'],
            $object['price'],
            $object['imageUrl'] // Передача параметра imageUrl
        );
    }

    private static function checkPropertyOrSetDefault(array &$object, $key, $defaultValue = ''): void
    {
        if (!isset($object[$key])) {
            $object[$key] = $defaultValue;
        }
    }

    /**
     * @return string
     */
    public function getSex(): string
    {
        return $this->for_women ? "да" : "нет";
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        return $this->product_type;
    }

    /**
     * @return bool
     */
    public function isForWomen(): bool
    {
        return $this->for_women;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string // Метод для получения imageUrl
    {
        return $this->imageUrl;
    }
}
