// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import static slim.TestSuite.getTestIncludePath;

import java.lang.reflect.Proxy;

import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;

import fitnesse.slim.SlimFactory;
import fitnesse.slim.SlimInstanceCreationTestBase;

public class PhpSlimInstanceCreationTest extends SlimInstanceCreationTestBase {

  private static SlimFactory slimFactory;

  @BeforeClass
  public static void setUpClass() {
    // Creates Bridge only once
    slimFactory = new PhpSlimFactory(getTestIncludePath());
  }

  @AfterClass
  public static void tearDownClass() {
    slimFactory.stop();
  }

  @Before
  public void setUp() throws Exception {
    caller = slimFactory.getStatementExecutor();
  }

  @Override
  protected void assertInstanceOfTestSlim(Object x) {
    assertTrue(x instanceof Proxy);
  }

  @Override
  protected String getTestClassPath() {
    return "TestModule";
  }

  @Test
  public void canSetActorFromInstanceStoredInSymbol() throws Exception {
    Object response = caller.create("x", getTestClassName(), new Object[0]);
    Object testSlim = caller.callAndAssign("X", "x", "getInstance",
        new Object[0]);
    response = caller.create("y", "$X", new Object[0]);
    assertEquals("OK", response);
    Object y = caller.getInstance("y");
    assertEquals(testSlim.toString(), y.toString());
    assertEquals("true", caller.call("x", "isSame", "$X"));
  }

}
