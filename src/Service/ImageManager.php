<?php
namespace App\Service;

use App\Entity\CategorySlider;
use App\Entity\Product;
use App\Entity\Slider;
use App\Entity\SliderSeller;
use App\Entity\User;
use App\Form\Model\AssertGallery;
use App\Form\Model\AssertProductImage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageManager
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var User
     */
    private $user;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var array
     */
    private $newImages;

    /**
     * @var FlashBagInterface
     */
    private $flash;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * Service Images Constructor.
     * @param Security $security
     * @param EntityManagerInterface $manager
     * @param ValidatorInterface $validator
     * @param FlashBagInterface $flash
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Security $security,
        EntityManagerInterface $manager,
        ValidatorInterface $validator,
        FlashBagInterface $flash,
        TranslatorInterface $translator
    ) {
        $this->security = $security;
        $this->user = $this->security->getUser();
        $this->manager = $manager;
        $this->validator = $validator;
        $this->flash = $flash;
        $this->newImages = [];
        $this->fileSystem = new Filesystem();
        $this->translator = $translator;
    }

    //<editor-fold desc="Code Profile">
    public function updateProfile($profileImage)
    {
        if ($profileImage) {
            $oldFileName = $this->user->getProfileImage();
            $url = "images/profile_image";
            $newProfileImageFilename = uniqid().'.'.$profileImage->guessExtension();
            try {
                $profileImage->move($url,$newProfileImageFilename);
                if(null != $oldFileName && true === $this->fileSystem->exists($url."/".$oldFileName))
                {
                    $this->fileSystem->remove([$url."/".$oldFileName]);
                }
                $this->user->setProfileImage($newProfileImageFilename);
                $this->manager->persist($this->user);
                $this->manager->flush();

                $this->flash->add('success', $this->translator->trans('Your Profile Image has been changed successfully'));

            } catch (\Exception $ex) {
                $this->flash->add('danger', $this->translator->trans('Error Profile Img'));
            }
        }
    }
    //</editor-fold>

    //<editor-fold desc="Code Cover">
    public function updateCover($coverImage)
    {
        if ($coverImage) {
            $oldFileName = $this->user->getCoverImage();
            $url = "images/cover_image";
            $newCoverImageFilename = uniqid().'.'.$coverImage->guessExtension();
            try {
                $coverImage->move($url,$newCoverImageFilename);
                if(null != $oldFileName && true === $this->fileSystem->exists($url."/".$oldFileName))
                {
                    $this->fileSystem->remove([$url."/".$oldFileName]);
                }
                $this->user->setCoverImage($newCoverImageFilename);
                $this->manager->persist($this->user);
                $this->manager->flush();

                $this->flash->add('success','Your Cover Image has been changed successfully');
            } catch (\Exception $ex) {
                $this->flash->add('error','Error Cover Img');
            }
        }
    }
    //</editor-fold>


    //<editor-fold desc="Code Image slider">
    public function addSliderImage($brochureFile,Slider $slider)
    {
        if ($brochureFile) {
            $url = 'images/slider';
            $newFilename = md5(uniqid()).'-'.(new DateTime())->format('Y_m_d_H_i_s').'.'.$brochureFile->guessExtension();
            try {
                $brochureFile->move($url,$newFilename);
            } catch (\Exception $ex){
                $this->flash->add('error','error');
            }
            $slider->setImageUrl($newFilename);
        }
    }

    public function updateSliderImage($brochureFile,Slider $slider)
    {
        if ($brochureFile) {
            $url = 'images/slider';
            $oldFileName = $slider->getImageUrl();
            $newFilename = md5(uniqid()).'-'.(new DateTime())->format('Y_m_d_H_i_s').'.'.$brochureFile->guessExtension();

            $brochureFile->move($url,$newFilename);

            if(true === $this->fileSystem->exists($url."/".$oldFileName))
            {
                $this->fileSystem->remove([$url.'/'.$oldFileName]);
            }
            $slider->setImageUrl($newFilename);
        }
    }

    public function deleteSliderImage(Slider $slider)
    {
        $url = 'images/slider';
        $oldFileName = $slider->getImageUrl();
        if(true === $this->fileSystem->exists($url."/".$oldFileName))
        {
            $this->fileSystem->remove([$url.'/'.$oldFileName]);
        }
    }

    //</editor-fold>

    //<editor-fold desc="Code Image category slider">
    public function addCategorySliderImage($brochureFile,CategorySlider $categorySlider)
    {
        if ($brochureFile) {
            $url = 'images/category-slider';
            $newFilename = md5(uniqid()).'-'.(new DateTime())->format('Y_m_d_H_i_s').'.'.$brochureFile->guessExtension();
            try {
                $brochureFile->move($url,$newFilename);
            } catch (\Exception $ex){
                $this->flash->add('error','error');
            }
            $categorySlider->setImageUrl($newFilename);
        }
    }

    public function updateCategorySliderImage($brochureFile,CategorySlider $categorySlider)
    {
        if ($brochureFile) {
            $url = 'images/category-slider';
            $oldFileName = $categorySlider->getImageUrl();
            $newFilename = md5(uniqid()).'-'.(new DateTime())->format('Y_m_d_H_i_s').'.'.$brochureFile->guessExtension();

            $brochureFile->move($url,$newFilename);

            if(true === $this->fileSystem->exists($url."/".$oldFileName))
            {
                $this->fileSystem->remove([$url.'/'.$oldFileName]);
            }
            $categorySlider->setImageUrl($newFilename);
        }
    }

    public function deleteCategorySliderImage(CategorySlider $categorySlider)
    {
        $url = 'images/category-slider';
        $oldFileName = $categorySlider->getImageUrl();
        if(true === $this->fileSystem->exists($url."/".$oldFileName))
        {
            $this->fileSystem->remove([$url.'/'.$oldFileName]);
        }
    }

    //</editor-fold>


    //<editor-fold desc="Code Image slider seller">
    public function addSliderSellerImage($brochureFile,SliderSeller $sliderSeller)
    {
        if ($brochureFile) {
            $url = 'images/slider-seller';
            $newFilename = md5(uniqid()).'-'.(new DateTime())->format('Y_m_d_H_i_s').'.'.$brochureFile->guessExtension();
            try {
                $brochureFile->move($url,$newFilename);
            } catch (\Exception $ex){
                $this->flash->add('error','error');
            }
            $sliderSeller->setImageUrl($newFilename);
        }
    }

    public function updateSliderSellerImage($brochureFile,SliderSeller $sliderSeller)
    {
        if ($brochureFile) {
            $url = 'images/slider-seller';
            $oldFileName = $sliderSeller->getImageUrl();
            $newFilename = md5(uniqid()).'-'.(new DateTime())->format('Y_m_d_H_i_s').'.'.$brochureFile->guessExtension();

            $brochureFile->move($url,$newFilename);

            if(true === $this->fileSystem->exists($url."/".$oldFileName))
            {
                $this->fileSystem->remove([$url.'/'.$oldFileName]);
            }
            $sliderSeller->setImageUrl($newFilename);
        }
    }

    public function deleteSliderSellerImage(SliderSeller $sliderSeller)
    {
        $url = 'images/slider-seller';
        $oldFileName = $sliderSeller->getImageUrl();
        if(true === $this->fileSystem->exists($url."/".$oldFileName))
        {
            $this->fileSystem->remove([$url.'/'.$oldFileName]);
        }
    }

    //</editor-fold>


    //<editor-fold desc="Code Image Product">
    public function saveProductImages(Product $product)
    {
        $url = 'images/product';
        if($product->getBrochures()){
            /** @var UploadedFile $pic */
            foreach($product->getBrochures() as $pic){
                if(!$this->saveImage($pic,$url)){break;}
            }
            $product->setImages($this->newImages);
        }
    }

    public function updateProductImages(Product $product,Request $request)
    {
        $url = 'images/product';
        $oldImagesRequest = (array)$request->request->get("oldPic");
        $oldImagesProduct = $product->getImages();
        $this->removeOldImages($oldImagesProduct,$oldImagesRequest,$url);
        if($product->getBrochures()){
            /** @var UploadedFile $pic */
            foreach($product->getBrochures() as $pic){
                if(!$this->saveImage($pic,$url)){break;}
            }
        }
        $product->setImages($this->newImages);
    }

    public function deleteProductImages(Product $product)
    {
        $url = 'images/product';
        $oldFileName = $product->getImages();
        foreach ($oldFileName as $src ){
            if($this->fileSystem->exists($url.$src)){
                $this->fileSystem->remove([$url.$src]);
            }
        }
    }
    //</editor-fold>


    //<editor-fold desc="Code Other functions file">
    public function createFolderUrl($url)
    {
        if(false === $this->fileSystem->exists($url))
        {
            $this->fileSystem->mkdir($url);
        }
    }

    public function removeOldImages($currantImages,$imagesRequest,$url)
    {
        foreach($currantImages as $src){
            if (!\in_array($src, $imagesRequest)) {
                $this->fileSystem->remove([$url.'/'.$src]);
            } else {
                $this->newImages[] = $src;
            }
        }
    }

    public function saveImage($pic,$url)
    {
        if('' !== $path = $pic->getClientOriginalName()){
            $ext = \pathinfo($path, PATHINFO_EXTENSION);
            $src = \uniqid().'.'.$ext;
            $pic->move($url, $src);
            if(count($this->newImages) < 10){
                $this->newImages[] = $src;
                return true;
            }else{return false;}
        }
    }
    //</editor-fold>

}