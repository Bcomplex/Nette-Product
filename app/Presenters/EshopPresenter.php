<?php


namespace app\Presenters;

use MongoDB\BSON\MaxKey;
use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;


class EshopPresenter extends Nette\Application\UI\Presenter
{
    private Nette\Database\Explorer $database;

    public function __construct(Nette\Database\Explorer $database)
    {
        $this->database = $database;
    }


 // render products

    public function renderProducts(): void
    {
        $this->template->products = $this->database
            ->table('products')
            ->limit(5);
    }

    public function actionList(): void
    {
        $this->template->form = '';
    }

    public function renderList(): void
    {
        $this->template->products = $this->database
            ->table('products')
            ->limit(10);

    }
 // render product

    public function renderShowProduct(int $id): void
    {
        $product = $this->database
            ->table('products')->get($id);
        if (!$product) {
            $this->error('Ups Stánka nexistuje');
        }
        $this->template->products = $product;
    }

    // edit products
    public function renderEdit(int $id): void
    {
      $product = $this->database
      ->table('products')
      ->get($id);
      if(!$product){
          $this->error('Products not found !!!');
      }
      $this->getComponent('productForm')
          ->setDefaults($product->toArray());
    }

    // delete
    public function handleDelete($id)
    {

        $product = $this->database
            ->table('products')
            ->where($id)
            ->delete();

        $this->flashMessage("Smazáno");
        $this->redirect("this");
    }

    protected function createComponentProductForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Název Produktu: ')
            ->setRequired();

        $form->addTextArea('description', 'Popis produktu: ')
            ->setRequired();

        $form->addText('amount', 'Počet kusu na skladě: ')
            ->setRequired();

        $form->addText('price', 'Cena produktu: ')
            ->setRequired();
        $form->addUpload('photo', 'Vložte obrázek produktu: ')
            ->addRule(Form::IMAGE, 'Image must be JPEG, PNG or GIF.')
            ->setRequired();

        $form->addText('alt', 'Popis obrázku: ')
            ->setRequired();

        $form->addSubmit('submit', 'Přidat produkt');
        $form->onSuccess[] = [$this, 'productFormSucceeded'];
        return $form;
    }


    public function handleAddForm(): void
    {
        $form = $this->template->renderToString(__DIR__ . '/templates/Eshop/form.latte');
        $this->template->form = $form;
        $this->redrawControl('form');
    }

     // insert to database

    public function productFormSucceeded(array $values): void

    {
        // echo'<pre>';
        // dump($values["photo"]);
        // echo'</pre>';

        // create random dir and move image to Image folder
        if ($values['photo']) {
            $dirName = $this->nameDir(8);
            $image = $values['photo'];
            $path = '\myBlog\www\image/' . $dirName . '/' . $image->getName();
            $paths='D:\app\xampp\htdocs'.$path;
            $image->move($paths);
        }
//        echo '<pre>';
//        dump($path);
//        echo '</pre>';
//        exit;
        $id = $this->getParameter('id');
        if($id){
            $product = $this->database
                ->table('products')
                ->where($id);
                $product->update([
                    'title' => $values['title'],
                    'descriptions' => $values['description'],
                    'amount' => $values['amount'],
                    'price' => $values['price'],
                    'photo' => $path,
                    'alt' => $values['alt'],

                ]);
        }else {
            $this->database
                ->table('products')
                ->insert([
                    'title' => $values['title'],
                    'descriptions' => $values['description'],
                    'amount' => $values['amount'],
                    'price' => $values['price'],
                    'photo' => $path,
                    'alt' => $values['alt'],

                ]);
        }
        $this->flashMessage('produkt byl přidán', 'success');
        $this->redirect('this');
    }
 // random dir name
    public function nameDir($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $str .= $characters[$index];
        }

        return $str;
    }
}
