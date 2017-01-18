<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type\Admin;

use Eccube\Common\Constant;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderType extends AbstractType
{

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;
        $config = $app['config'];
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $builder
            ->add('name', NameType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('kana', KanaType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('company_name', TextType::class, array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    ))
                ),
            ))
            ->add('zip', ZipType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('address', AddressType::class, array(
                'required' => false,
                'pref_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
                'addr01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
                'addr02_options' => array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
            ))
            ->add('email', EmailType::class, array(
                'required' => false,
                'label' => 'メールアドレス',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                ),
            ))
            ->add('tel', TelType::class, array(
                'required' => false,
                'options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ),
            ))
            ->add('fax', TelType::class, array(
                'label' => 'FAX番号',
                'required' => false,
            ))
            ->add('company_name', TextType::class, array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    ))
                ),
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'お問い合わせ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['ltext_len'],
                    )),
                ),
            ))
            ->add('discount', MoneyType::class, array(
                'label' => '値引き',
                'currency' => 'JPY',
                'scale' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('delivery_fee_total', MoneyType::class, array(
                'label' => '送料',
                'currency' => 'JPY',
                'scale' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('charge', MoneyType::class, array(
                'label' => '手数料',
                'currency' => 'JPY',
                'scale' => 0,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $config['int_len'],
                    )),
                ),
            ))
            ->add('note', TextareaType::class, array(
                'label' => 'SHOP用メモ欄',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['ltext_len'],
                    )),
                ),
            ))
            ->add('OrderStatus', EntityType::class, array(
                'class' => 'Eccube\Entity\Master\OrderStatus',
                // 'property' => 'name',
                // FIXME 'empty_value' => '選択してください',
                // 'empty_data' => null,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.rank', 'ASC');
                },
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('Payment', EntityType::class, array(
                'required' => false,
                'class' => 'Eccube\Entity\Payment',
                // 'property' => 'method',
                // FIXME 'empty_value' => '選択してください',
                // 'empty_data' => null,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('OrderDetails', CollectionType::class, array(
                'entry_type' => OrderDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
            ->add('Shippings', CollectionType::class, array(
                'entry_type' => ShippingType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ));
        $builder
            ->add($builder->create('Customer', HiddenType::class)
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->app['orm.em'],
                    '\Eccube\Entity\Customer'
                )));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($BaseInfo) {

            $data = $event->getData();
            $orderDetails = &$data['OrderDetails'];

            // 数量0フィルター
            $quantityFilter = function ($v) {
                return !(isset($v['quantity']) && preg_match('/^0+$/', trim($v['quantity'])));
            };

            if ($BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {

                $shippings = &$data['Shippings'];

                // 数量を抽出
                $getQuantity = function ($v) {
                    return (isset($v['quantity']) && preg_match('/^\d+$/', trim($v['quantity']))) ?
                        trim($v['quantity']) :
                        0;
                };

                foreach ($shippings as &$shipping) {
                    if (!empty($shipping['ShipmentItems'])) {
                        $shipping['ShipmentItems'] = array_filter($shipping['ShipmentItems'], $quantityFilter);
                    }
                }

                if (!empty($orderDetails)) {

                    foreach ($orderDetails as &$orderDetail) {

                        $orderDetail['quantity'] = 0;

                        // 受注詳細と同じ商品規格のみ抽出
                        $productClassFilter = function ($v) use ($orderDetail) {
                            return $orderDetail['ProductClass'] === $v['ProductClass'];
                        };

                        foreach ($shippings as &$shipping) {

                            if (!empty($shipping['ShipmentItems'])) {

                                // 同じ商品規格の受注詳細の価格を適用
                                $applyPrice = function (&$v) use ($orderDetail) {
                                    $v['price'] = ($v['ProductClass'] === $orderDetail['ProductClass']) ?
                                        $orderDetail['price'] :
                                        $v['price'];
                                };
                                array_walk($shipping['ShipmentItems'], $applyPrice);

                                // 数量適用
                                $relatedShipmentItems = array_filter($shipping['ShipmentItems'], $productClassFilter);
                                $quantities = array_map($getQuantity, $relatedShipmentItems);
                                $orderDetail['quantity'] += array_sum($quantities);
                            }
                        }
                    }
                }
            }

            if (!empty($orderDetails)) {
                $data['OrderDetails'] = array_filter($orderDetails, $quantityFilter);
            }

            $event->setData($data);
        });
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $orderDetails = $form['OrderDetails']->getData();
            if (empty($orderDetails) || count($orderDetails) < 1) {
                // 画面下部にエラーメッセージを表示させる
                $form['charge']->addError(new FormError('商品が追加されていません。'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Order',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order';
    }
}
